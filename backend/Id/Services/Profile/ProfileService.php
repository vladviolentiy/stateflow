<?php

namespace Flow\Id\Services\Profile;

use Flow\Id\Services\BaseController;
use Flow\Id\Models\Password;
use Flow\Id\Models\PrivateKey;
use Flow\Id\Storage\Interfaces\UserStorageInterface;

class ProfileService extends BaseController
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
