<?php

namespace Flow\Id\Storage\Interfaces;

use Flow\Id\ValueObject\EncryptedData;
use Flow\Id\ValueObject\Password;
use Flow\Id\ValueObject\PrivateKey;
use Flow\Id\ValueObject\RsaPublicKey;
use Symfony\Component\Uid\Uuid;

interface UserStorageInterface
{
    /**
     * @param non-empty-string $hashedEmail
     * @return array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null
     */
    public function getUserByEmail(string $hashedEmail): ?array;

    /**
     * @param non-empty-string $hashedPhone
     * @return array{userId:positive-int,salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null
     */
    public function getUserByPhone(string $hashedPhone): ?array;

    /**
     * @param Uuid $uuid
     * @return array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null
     */
    public function getUserByUUID(Uuid $uuid): ?array;

    /**
     * @param Uuid $uuid
     * @param Password $password
     * @param non-empty-string $iv
     * @param non-empty-string $salt
     * @param EncryptedData $fNameEncrypted
     * @param EncryptedData $lNameEncrypted
     * @param EncryptedData $bDayEncrypted
     * @param non-empty-string $globalHash
     * @return positive-int
     */
    public function insertUser(Uuid $uuid, Password $password, string $iv, string $salt, EncryptedData $fNameEncrypted, EncryptedData $lNameEncrypted, EncryptedData $bDayEncrypted, string $globalHash): int;

    /**
     * @param positive-int $userId
     * @param RsaPublicKey $publicKey
     * @param PrivateKey $encryptedPrivateKey
     * @return void
     */
    public function insertNewEncryptInfo(int $userId, RsaPublicKey $publicKey, PrivateKey $encryptedPrivateKey): void;

    /**
     * @param positive-int $userId
     * @return array{fNameEncrypted:non-empty-string,lNameEncrypted:non-empty-string,bDayEncrypted:non-empty-string}|null
     */
    public function getBasicInfo(int $userId): ?array;

    /**
     * @param positive-int $userId
     * @param Password $newPassword
     * @return void
     */
    public function updatePassword(int $userId, Password $newPassword): void;

    /**
     * @param positive-int $userId
     * @param PrivateKey $privateKey
     * @return void
     */
    public function updateUserPrivateKey(int $userId, PrivateKey $privateKey): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
