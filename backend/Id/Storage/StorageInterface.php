<?php

namespace Flow\Id\Storage;

use Flow\Core\Exceptions\DatabaseException;
use Flow\Id\Models\Password;
use Flow\Id\Models\PrivateKey;
use Symfony\Component\Uid\Uuid;

interface StorageInterface
{
    /**
     * @param non-empty-string $hashedEmail
     * @return array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null
     * @throws DatabaseException
     */
    public function getUserByEmail(string $hashedEmail): ?array;

    /**
     * @param non-empty-string $hashedPhone
     * @return array{userId:positive-int,salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null
     * @throws DatabaseException
     */
    public function getUserByPhone(string $hashedPhone): ?array;

    /**
     * @param Uuid $uuid
     * @return array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null
     * @throws DatabaseException
     */
    public function getUserByUUID(Uuid $uuid): ?array;

    /**
     * @param Uuid $uuid
     * @param Password $password
     * @param non-empty-string $iv
     * @param non-empty-string $salt
     * @param non-empty-string $fNameEncrypted
     * @param non-empty-string $lNameEncrypted
     * @param non-empty-string $bDayEncrypted
     * @param non-empty-string $globalHash
     * @return positive-int
     */
    public function insertUser(Uuid $uuid, Password $password, string $iv, string $salt, string $fNameEncrypted, string $lNameEncrypted, string $bDayEncrypted, string $globalHash): int;

    /**
     * @param positive-int $userId
     * @param non-empty-string $publicKey
     * @param PrivateKey $encryptedPrivateKey
     * @return void
     */
    public function insertNewEncryptInfo(int $userId, string $publicKey, PrivateKey $encryptedPrivateKey): void;

    /**
     * @param non-empty-string $hash
     * @param positive-int $userId
     * @return void
     */
    public function insertSession(string $hash, int $userId): void;

    /**
     * @param non-empty-string $token
     * @return array{userId:positive-int,lang:non-empty-string,sessionId:positive-int}|null
     * @throws DatabaseException
     */
    public function checkIssetToken(string $token): ?array;

    /**
     * @param positive-int $userId
     * @return list<array{id:positive-int,email:string}>
     * @throws DatabaseException
     */
    public function getEmailList(int $userId): array;

    /**
     * @param positive-int $userId
     * @param non-empty-string $encryptedEmail
     * @param non-empty-string $emailHash
     * @param bool $allowAuth
     * @return int
     */
    public function insertNewEmail(int $userId, string $encryptedEmail, string $emailHash, bool $allowAuth): int;

    /**
     * @param positive-int $userId
     * @param positive-int $itemId
     * @param non-empty-string $encryptedEmail
     * @param non-empty-string $emailHash
     * @param bool $allowAuth
     * @return void
     */
    public function editEmailItem(int $userId, int $itemId, string $encryptedEmail, string $emailHash, bool $allowAuth): void;

    /**
     * @param positive-int $userId
     * @param positive-int $itemId
     * @return array{emailEncrypted:string,allowAuth:int}|null
     * @throws DatabaseException
     */
    public function getEmailItem(int $userId, int $itemId): ?array;

    /**
     * @param positive-int $userId
     * @param positive-int $itemId
     * @return void
     */
    public function deleteEmail(int $userId, int $itemId): void;

    /**
     * @param positive-int $userId
     * @return list<array{id:int,phone:string}>
     * @throws DatabaseException
     */
    public function getPhonesList(int $userId): array;

    /**
     * @param positive-int $userId
     * @param positive-int $itemId
     * @return void
     */
    public function deletePhone(int $userId, int $itemId): void;

    /**
     * @param positive-int $userId
     * @param positive-int $itemId
     * @return array{phoneEncrypted:string,allowAuth:int}|null
     * @throws DatabaseException
     */
    public function getPhoneItem(int $userId, int $itemId): ?array;

    /**
     * @param positive-int $userId
     * @param non-empty-string $phoneEncrypted
     * @param non-empty-string $phoneHash
     * @param bool $allowAuth
     * @return positive-int
     */
    public function insertNewPhone(int $userId, string $phoneEncrypted, string $phoneHash, bool $allowAuth): int;

    /**
     * @param non-empty-string $emailHash
     * @return bool
     */
    public function checkEmailInDatabase(string $emailHash): bool;

    /**
     * @param non-empty-string $phoneHash
     * @return bool
     */
    public function checkPhoneInDatabase(string $phoneHash): bool;

    /**
     * @param positive-int $userId
     * @return list<array{authHash:non-empty-string,uas:non-empty-string,ips:non-empty-string,createdAt:non-empty-string}>
     * @throws \VladViolentiy\VivaFramework\Exceptions\DatabaseException
     */
    public function getSessionsForUser(int $userId): array;

    /**
     * @param positive-int $userId
     * @param non-empty-string $hash
     * @return void
     */
    public function killSession(int $userId, string $hash): void;

    /**
     * @param non-empty-string $session
     * @param non-empty-string $encryptedIp
     * @param non-empty-string $encryptedUa
     * @param non-empty-string $encryptedAE
     * @param non-empty-string $encryptedAL
     * @return positive-int|null
     */
    public function checkIssetSessionMetaInfo(
        string $session,
        string $encryptedIp,
        string $encryptedUa,
        string $encryptedAE,
        string $encryptedAL,
    ): ?int;

    /**
     * @param positive-int $sessionId
     * @param non-empty-string $encryptedIp
     * @param non-empty-string $encryptedUa
     * @param non-empty-string $encryptedAE
     * @param non-empty-string $encryptedAL
     * @param non-empty-string $encryptedLastSeenAt
     * @return void
     */
    public function insertSessionMeta(
        int $sessionId,
        string $encryptedIp,
        string $encryptedUa,
        string $encryptedAE,
        string $encryptedAL,
        string $encryptedLastSeenAt,
    ): void;

    /**
     * @param positive-int $sessionMetaInfoId
     * @param non-empty-string $encryptedLastSeenAt
     * @return void
     */
    public function updateLastSeenSessionMeta(
        int $sessionMetaInfoId,
        string $encryptedLastSeenAt,
    ): void;

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
