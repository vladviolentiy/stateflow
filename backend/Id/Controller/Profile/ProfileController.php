<?php

namespace Flow\Id\Controller\Profile;

use Flow\Id\Controller\AuthenticateBaseController;
use Flow\Id\Models\Password;
use Flow\Id\Models\PrivateKey;

class ProfileController extends AuthenticateBaseController
{
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
