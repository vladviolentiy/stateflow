<?php

namespace Flow\Id\Mappers;

use Flow\Id\Resources\GetEmailItemResource;
use Flow\Id\Storage\Interfaces\EmailStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\NotfoundException;

readonly class EmailMapper
{
    /**
     * @param EmailStorageInterface $emailStorage
     * @param positive-int $userId
     */
    public function __construct(
        private EmailStorageInterface $emailStorage,
        private int $userId,
    ) {}

    /**
     * @param positive-int $id
     * @return GetEmailItemResource
     * @throws NotfoundException
     */
    public function getItemById(int $id): GetEmailItemResource
    {
        $data = $this->emailStorage->getItemById($this->userId, $id);

        if ($data === null) {
            throw new NotfoundException();
        }

        return GetEmailItemResource::fromState($data);
    }
}
