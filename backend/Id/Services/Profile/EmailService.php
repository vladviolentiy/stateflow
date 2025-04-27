<?php

namespace Flow\Id\Services\Profile;

use Flow\Core\Exceptions\DatabaseException;
use Flow\Id\Services\BaseController;
use Flow\Id\Storage\Interfaces\EmailStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\NotfoundException;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class EmailService extends BaseController
{
    /**
     * @param EmailStorageInterface $storage
     * @param positive-int $userId
     */
    public function __construct(
        private readonly EmailStorageInterface $storage,
        private readonly int $userId,
    ) {
        parent::__construct();
    }

    /**
     * @return list<array{id:int,email:string}>
     * @throws DatabaseException
     */
    public function getEmailList(): array
    {
        return $this->storage->getByUserId($this->userId);
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

        $id = $this->storage->insertNew($this->userId, $emailEncrypted, $emailHash, $allowAuth);

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
        \Flow\Core\Validation::encryptedData($emailEncrypted, 'email');

        $emailHash = hash('sha384', $this->appToken . $emailHash);

        $this->storage->editItem($itemId, $this->userId, $emailEncrypted, $emailHash, $allowAuth);
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

        $i = $this->storage->getItemById($this->userId, $itemId);
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
        $this->storage->deleteItem($this->userId, $itemId);
    }
}
