<?php

namespace Flow\Id\Controller\Profile;

use Flow\Core\Exceptions\DatabaseException;
use Flow\Core\Exceptions\NotfoundException;
use Flow\Id\Controller\AuthenticateBaseController;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class EmailController extends AuthenticateBaseController
{
    /**
     * @return list<array{id:int,email:string}>
     * @throws DatabaseException
     */
    public function getEmailList(): array
    {
        return $this->storage->getEmailList($this->userId);
    }

    /**
     * @param string $emailEncrypted
     * @param string $emailHash
     * @param bool $allowAuth
     * @return int
     */
    public function addNewEmail(string $emailEncrypted, string $emailHash, bool $allowAuth): int
    {
        Validation::nonEmpty($emailHash);
        Validation::nonEmpty($emailEncrypted);

        \Flow\Core\Validation::encryptedData($emailEncrypted);

        $emailHash = hash('sha384', $this->appToken . $emailHash);

        $id = $this->storage->insertNewEmail($this->userId, $emailEncrypted, $emailHash, $allowAuth);

        return $id;
    }

    /**
     * @param int $itemId
     * @param string $emailEncrypted
     * @param string $emailHash
     * @param bool $allowAuth
     * @throws ValidationException
     */
    public function editItem(int $itemId, string $emailEncrypted, string $emailHash, bool $allowAuth): void
    {
        Validation::id($itemId);
        Validation::hash($emailHash);
        Validation::nonEmpty($emailEncrypted);
        \Flow\Core\Validation::encryptedData($emailEncrypted);

        $emailHash = hash('sha384', $this->appToken . $emailHash);


        $this->storage->editEmailItem($itemId, $this->userId, $emailEncrypted, $emailHash, $allowAuth);
    }

    /**
     * @param int $itemId
     * @return array{emailEncrypted:string,allowAuth:bool}
     * @throws DatabaseException
     * @throws NotfoundException
     */
    public function getEmailItem(int $itemId): array
    {
        Validation::id($itemId);


        $i = $this->storage->getEmailItem($this->userId, $itemId);
        if ($i === null) {
            throw new NotfoundException();
        }
        $i['allowAuth'] = (bool) $i['allowAuth'];

        return $i;

    }

    /**
     * @param int $itemId
     * @return void
     * @throws ValidationException
     */
    public function deleteEmail(int $itemId): void
    {
        Validation::id($itemId);
        $this->storage->deleteEmail($this->userId, $itemId);
    }
}
