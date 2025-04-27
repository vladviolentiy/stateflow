<?php

namespace Flow\Id\Services\Profile;

use Flow\Id\Services\BaseController;
use Flow\Id\Storage\Interfaces\SessionStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\DatabaseException;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class SessionsService extends BaseController
{
    /**
     * @param SessionStorageInterface $storage
     * @param positive-int $userId
     */
    public function __construct(
        private readonly SessionStorageInterface $storage,
        private readonly int $userId,
    ) {
        parent::__construct();
    }

    /**
     * @return list<array{authHash:non-empty-string,uas:non-empty-string[],ips:non-empty-string[],createdAt:non-empty-string}>
     * @throws DatabaseException
     */
    public function get(): array
    {
        $i = $this->storage->getSessionsForUser($this->userId);
        $i = array_map(function ($item) {
            /** @var non-empty-string[] $uas */
            $uas = explode(',', $item['uas']);
            $item['uas'] = $uas;
            /** @var non-empty-string[] $ips */
            $ips = explode(',', $item['ips']);
            $item['ips'] = $ips;

            return $item;
        }, $i);

        return $i;
    }

    /**
     * @param string $hash
     * @param bool $returnAvailable
     * @return list<array{authHash:non-empty-string,uas:non-empty-string[],ips:non-empty-string[],createdAt:non-empty-string}>|null
     * @throws DatabaseException
     * @throws ValidationException
     */
    public function killSession(
        string $hash,
        bool   $returnAvailable,
    ): ?array {
        $hash = mb_strtolower($hash);
        Validation::hash($hash);
        $this->storage->killSession($this->userId, $hash);
        if ($returnAvailable) {
            return $this->get();
        } else {
            return null;
        }
    }

    public function writeHashInfo(
        string $session,
        string $encryptedIp,
        string $encryptedUa,
        string $encryptedAE,
        string $encryptedAL,
        string $encryptedLastSeen,
    ): void {
        Validation::hash($session);
        Validation::nonEmpty($encryptedIp);
        Validation::nonEmpty($encryptedUa);
        Validation::nonEmpty($encryptedAE);
        Validation::nonEmpty($encryptedAL);
        Validation::nonEmpty($encryptedLastSeen);

        \Flow\Core\Validation::encryptedData($encryptedIp, 'ip');
        \Flow\Core\Validation::encryptedData($encryptedUa, 'ua');
        \Flow\Core\Validation::encryptedData($encryptedAE, 'accept encoding');
        \Flow\Core\Validation::encryptedData($encryptedAL, 'accept language');
        \Flow\Core\Validation::encryptedData($encryptedLastSeen, 'last seen at');

        $metaId = $this->storage->checkIssetSessionMetaInfo($session, $encryptedIp, $encryptedUa, $encryptedAE, $encryptedAL);

        if ($metaId === null) {
            /** @var array{userId:positive-int,lang:string,sessionId:positive-int} $sessionId */
            $sessionId = $this->storage->checkIssetToken($session);
            $this->storage->insertSessionMeta($sessionId['sessionId'], $encryptedIp, $encryptedUa, $encryptedAE, $encryptedAL, $encryptedLastSeen);
        } else {
            $this->storage->updateLastSeenSessionMeta($metaId, $encryptedLastSeen);
        }

    }
}
