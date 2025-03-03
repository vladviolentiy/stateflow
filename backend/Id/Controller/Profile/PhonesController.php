<?php

namespace Flow\Id\Controller\Profile;

use Flow\Core\Exceptions\DatabaseException;
use Flow\Id\Controller\AuthenticateBaseController;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class PhonesController extends AuthenticateBaseController
{
    /**
     * @return list<array{id:int,phone:string}>
     * @throws DatabaseException
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
