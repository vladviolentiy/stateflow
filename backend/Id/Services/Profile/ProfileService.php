<?php

namespace Flow\Id\Services\Profile;

use Flow\Id\Services\BaseService;
use Flow\Id\ValueObject\Password;
use Flow\Id\ValueObject\PrivateKey;
use Flow\Id\Storage\Interfaces\UserStorageInterface;

class ProfileService extends BaseService
{
    /**
     * @param UserStorageInterface $storage
     * @param positive-int $userId
     */
    public function __construct(
        private readonly UserStorageInterface $storage,
        private readonly int $userId,
    ) {
        parent::__construct();
    }

    public function updatePassword(
        string $newPassword,
        string $encryptionKey,
    ): void {
        $passwordObject = new Password($newPassword);
        $encryptedKey = new PrivateKey($encryptionKey);

        $this->storage->beginTransaction();
        $this->storage->updatePassword($this->userId, $passwordObject);
        $this->storage->updateUserPrivateKey($this->userId, $encryptedKey);
        $this->storage->commit();
    }
}
