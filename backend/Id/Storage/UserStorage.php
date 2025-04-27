<?php

namespace Flow\Id\Storage;

use Flow\Id\Models\EncryptedData;
use Flow\Id\Models\Password;
use Flow\Id\Models\PrivateKey;
use Flow\Id\Models\RsaPublicKey;
use Flow\Id\Storage\Interfaces\UserStorageInterface;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Databases\MysqliV2;

final class UserStorage extends MysqliV2 implements UserStorageInterface
{
    public function __construct()
    {
        $this->setDb(DatabaseSingleton::getInstance());

    }

    public function getUserByEmail(string $hashedEmail): ?array
    {
        /** @var array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null $info */
        $info = $this->executeQuery('SELECT u.id as userId, salt, iv
FROM usersEmails 
    JOIN users u on usersEmails.userId = u.id
WHERE emailHash=unhex(?) and allowAuth=true and deleted=false', [$hashedEmail])->fetch_array(MYSQLI_ASSOC);
        if ($info === null) {
            return null;
        }

        return $info;
    }

    public function getUserByUUID(Uuid $uuid): ?array
    {
        /** @var array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null $info */
        $info = $this->executeQuery('SELECT id as userId, salt, iv, password
FROM users
WHERE uuid=unhex(?)', [$uuid->toBinary()])->fetch_array(MYSQLI_ASSOC);
        if ($info === null) {
            return null;
        }

        return $info;
    }

    public function getUserByPhone(string $hashedPhone): ?array
    {
        /** @var array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null $info */
        $info = $this->executeQuery('SELECT users.id as userId,salt,iv, password
FROM users
    JOIN usersPhones uP on users.id = uP.userId
WHERE phoneHash=unhex(?)', [$hashedPhone])->fetch_array(MYSQLI_ASSOC);
        if ($info === null) {
            return null;
        }

        return $info;
    }

    public function insertUser(Uuid $uuid, Password $password, string $iv, string $salt, EncryptedData $fNameEncrypted, EncryptedData $lNameEncrypted, EncryptedData $bDayEncrypted, string $globalHash): int
    {
        $this->executeQueryBool(
            'INSERT INTO users(uuid, password, iv, salt, fNameEncrypted, lNameEncrypted, bDayEncrypted, globalHash) VALUES(?,?,?,?,?,?,?,unhex(?))',
            [$uuid->toBinary(), $password->value, $iv, $salt, $fNameEncrypted->value, $lNameEncrypted->value, $bDayEncrypted->value, $globalHash],
        );
        /** @var positive-int $insId */
        $insId = $this->insertId();

        return $insId;
    }

    public function insertNewEncryptInfo(int $userId, RsaPublicKey $publicKey, PrivateKey $encryptedPrivateKey): void
    {
        $this->executeQueryBool(
            'INSERT INTO usersEncryptInfo(userId, publicKey, encryptedPrivateKey) VALUES(?,?,?)',
            [$userId, $publicKey->value, $encryptedPrivateKey->value],
        );
    }

    public function getBasicInfo(int $userId): ?array
    {
        /** @var array{fNameEncrypted:non-empty-string,lNameEncrypted:non-empty-string,bDayEncrypted:non-empty-string}|null $i */
        $i = $this->executeQuery('SELECT fNameEncrypted, lNameEncrypted, bDayEncrypted FROM users WHERE id=?', [$userId])->fetch_array(MYSQLI_ASSOC);

        return $i;
    }

    public function updatePassword(int $userId, Password $newPassword): void
    {
        $this->executeQueryBool('UPDATE `users` SET `password`=? WHERE id=?', [$newPassword->value, $userId]);
    }

    public function updateUserPrivateKey(int $userId, PrivateKey $privateKey): void
    {
        $this->executeQueryBool("UPDATE `usersEncryptInfo` SET encryptedPrivateKey=? WHERE id=? and type='default'", [$privateKey->value, $userId]);
    }
}
