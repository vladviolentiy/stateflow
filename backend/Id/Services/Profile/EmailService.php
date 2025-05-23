<?php

namespace Flow\Id\Services\Profile;

use Flow\Id\Mappers\EmailMapper;
use Flow\Id\Resources\GetEmailItemResource;
use Flow\Id\Services\BaseService;
use Flow\Id\Storage\Interfaces\EmailStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class EmailService extends BaseService
{
    private readonly EmailMapper $userEmailMapper;

    /**
     * @param EmailStorageInterface $storage
     * @param positive-int $userId
     */
    public function __construct(
        private readonly EmailStorageInterface $storage,
        private readonly int                   $userId,
    ) {
        parent::__construct();
        $this->userEmailMapper = new EmailMapper($this->storage, $this->userId);
    }

    /**
     * @return list<array{id:int,email:string}>
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
     * @return GetEmailItemResource
     */
    public function getEmailItem(int $itemId): GetEmailItemResource
    {
        Validation::id($itemId);

        $i = $this->userEmailMapper->getItemById($itemId);

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
