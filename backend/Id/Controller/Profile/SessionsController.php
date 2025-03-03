<?php

namespace Flow\Id\Controller\Profile;

use Flow\Id\Controller\AuthenticateBaseController;
use VladViolentiy\VivaFramework\Exceptions\DatabaseException;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class SessionsController extends AuthenticateBaseController
{
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
}
