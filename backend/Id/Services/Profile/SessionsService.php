<?php

namespace Flow\Id\Services\Profile;

use Flow\Id\DTO\WriteMetaInfoDTO;
use Flow\Id\Services\BaseService;
use Flow\Id\Storage\Interfaces\SessionStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\DatabaseException;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class SessionsService extends BaseService
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
        bool $returnAvailable,
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

    /**
     * @param WriteMetaInfoDTO $info
     * @param positive-int $sessionId
     * @return void
     */
    public function writeHashInfo(WriteMetaInfoDTO $info, int $sessionId): void
    {
        $metaId = $this->storage->checkIssetSessionMetaInfo($info->token, $info->ip, $info->userAgent, $info->acceptEncoding, $info->acceptLanguage);

        if ($metaId === null) {
            $this->storage->insertSessionMeta($sessionId, $info->ip, $info->userAgent, $info->acceptEncoding, $info->acceptLanguage, $info->lastSeen);
        } else {
            $this->storage->updateLastSeenSessionMeta($metaId, $info->lastSeen);
        }

    }
}
