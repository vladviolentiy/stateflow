<?php

namespace Flow\Id\Mappers;

use Flow\Id\Model\UserEmail;
use Flow\Id\Storage\Interfaces\EmailStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\NotfoundException;

readonly class UserEmailMapper
{
    /**
     * @param EmailStorageInterface $emailStorage
     * @param positive-int $userId
     */
    public function __construct(
        private EmailStorageInterface $emailStorage,
        private int $userId,
    ) {}

    public function getItemById(int $id): UserEmail
    {
        $data = $this->emailStorage->getItemById($this->userId, $id);

        if ($data === null) {
            throw new NotfoundException();
        }

        return new UserEmail(
            $id,
            $this->userId,
            null,
            $data['emailEncrypted'],
            (bool) $data['allowAuth'],
            null,
        );

    }
}
