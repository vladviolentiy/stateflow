<?php

namespace Flow\Id\Services\Profile;

use Flow\Id\Services\BaseService;
use Flow\Id\Storage\Interfaces\PhoneStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class PhonesService extends BaseService
{
    /**
     * @param PhoneStorageInterface $storage
     * @param positive-int $userId
     */
    public function __construct(
        private readonly PhoneStorageInterface $storage,
        private readonly int $userId,
    ) {
        parent::__construct();
    }

    /**
     * @return list<array{id:int,phone:string}>
     */
    public function get(): array
    {
        return $this->storage->getPhonesList($this->userId);
    }

    public function addNewPhone(
        string $phoneEncrypted,
        string $phoneHash,
        bool $allowAuth,
    ): int {
        Validation::nonEmpty($phoneEncrypted);
        Validation::nonEmpty($phoneHash);

        $phoneHash = hash('sha384', $this->appToken . $phoneHash);

        if ($this->storage->checkPhoneInDatabase($phoneHash)) {
            throw new ValidationException('Номер уже существует в БД');
        }
        $id = $this->storage->insertNewPhone($this->userId, $phoneEncrypted, $phoneHash, $allowAuth);

        return $id;
    }
}
