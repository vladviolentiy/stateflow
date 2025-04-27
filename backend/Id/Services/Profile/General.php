<?php

namespace Flow\Id\Services\Profile;

use Flow\Id\Storage\Interfaces\UserStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\NotfoundException;

readonly class General
{
    /**
     * @param UserStorageInterface $storage
     * @param positive-int $userId
     */
    public function __construct(
        private UserStorageInterface $storage,
        private int $userId,
    ) {}

    /**
     * @return array{fNameEncrypted:non-empty-string,lNameEncrypted:non-empty-string,bDayEncrypted:non-empty-string}
     */
    public function getBasicInfo(): array
    {
        $info = $this->storage->getBasicInfo($this->userId);

        if ($info === null) {
            throw new NotfoundException();
        }

        return $info;
    }
}
