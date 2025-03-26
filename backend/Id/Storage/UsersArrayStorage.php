<?php

namespace Flow\Id\Storage;

use Symfony\Component\Uid\Uuid;

class UsersArrayStorage implements StorageInterface
{
    /**
     * @var list<array{id:int, uuid:string, salt:string, iv:string, password: non-empty-string}>
     */
    private array $users = [];
    /**
     * @var list<array{userId:int, emailHash:non-empty-string}>
     */
    private array $usersEmail = [];
    /** @var list<array{id: positive-int, userId: positive-int, phone: non-empty-string, phoneHash:non-empty-string, allowAuth:bool}>  */
    private array $usersPhones = [];

    public function getUserByEmail(string $hashedEmail): ?array
    {
        return null;
    }

    public function getUserByPhone(string $hashedPhone): ?array
    {
        return null;
    }

    public function getUserByUUID(Uuid $uuid): ?array
    {
        foreach ($this->users as $item) {
            if ($uuid->toRfc4122() === $item['uuid']) {
                return [
                    'password' => $item['password'],
                    'userId' => $item['id'],
                    'salt' => $item['salt'],
                    'iv' => $item['iv'],
                ];
            }
        }

        return null;
    }

    public function checkIssetUUID(string $uuid): bool
    {
        return false;
    }

    public function insertUser(Uuid $uuid, string $password, string $iv, string $salt, string $fNameEncrypted, string $lNameEncrypted, string $bDayEncrypted, string $globalHash): int
    {
        $userId = count($this->users) + 1;
        $this->users[] = [
            'id' => $userId,
            'uuid' => $uuid->toRfc4122(),
            'password' => $password,
            'iv' => $iv,
            'salt' => $salt,
            'fName' => $fNameEncrypted,
            'lName' => $lNameEncrypted,
            'bDay' => $bDayEncrypted,
            'hash' => $globalHash,
        ];

        return $userId;
    }

    public function insertNewEncryptInfo(int $userId, string $publicKey, string $encryptedPrivateKey): void
    {
        //TODO: Phpstan error is never read. not implemented

        //        $this->keysStorage[] = [
        //            "userId"=>$userId,
        //            "private"=>$encryptedPrivateKey,
        //            "public"=>$publicKey
        //        ];
    }

    public function insertSession(string $hash, int $userId): void
    {
        // TODO: Implement insertSession() method.
    }

    public function checkIssetToken(string $token): ?array
    {
        return null;
    }

    public function getEmailList(int $userId): array
    {
        return [];
    }

    public function insertNewEmail(int $userId, string $encryptedEmail, string $emailHash, bool $allowAuth): int
    {
        return 0;
    }

    public function editEmailItem(int $userId, int $itemId, string $encryptedEmail, string $emailHash, bool $allowAuth): void
    {
        // TODO: Implement editEmailItem() method.
    }

    public function getEmailItem(int $userId, int $itemId): ?array
    {
        return null;
    }

    public function deleteEmail(int $userId, int $itemId): void
    {
        // TODO: Implement deleteEmail() method.
    }

    public function getPhonesList(int $userId): array
    {
        return $this->usersPhones;
    }

    public function deletePhone(int $userId, int $itemId): void
    {
        // TODO: Implement deletePhone() method.
    }

    public function getPhoneItem(int $userId, int $itemId): ?array
    {
        return null;
    }

    public function insertNewPhone(int $userId, string $phoneEncrypted, string $phoneHash, bool $allowAuth): int
    {
        $id = count($this->usersPhones) + 1;
        $this->usersPhones[] = [
            'id' => $id,
            'userId' => $id,
            'phone' => $phoneEncrypted,
            'phoneHash' => $phoneHash,
            'allowAuth' => $allowAuth,
        ];

        return $id;
    }

    public function checkEmailInDatabase(string $emailHash): bool
    {
        foreach ($this->usersEmail as $item) {
            if ($item['emailHash'] === $emailHash) {
                return true;
            }
        }

        return false;
    }

    public function checkPhoneInDatabase(string $phoneHash): bool
    {
        return false;
    }

    public function getSessionsForUser(int $userId): array
    {
        return [];
    }

    public function killSession(int $userId, string $hash): void
    {
        // TODO: Implement killSession() method.
    }

    public function checkIssetSessionMetaInfo(string $session, string $encryptedIp, string $encryptedUa, string $encryptedAE, string $encryptedAL): ?int
    {
        return null;
    }

    public function insertSessionMeta(int $sessionId, string $encryptedIp, string $encryptedUa, string $encryptedAE, string $encryptedAL, string $encryptedLastSeenAt): void
    {
        // TODO: Implement insertSessionMeta() method.
    }

    public function updateLastSeenSessionMeta(int $sessionMetaInfoId, string $encryptedLastSeenAt): void
    {
        // TODO: Implement updateLastSeenSessionMeta() method.
    }

    public function getBasicInfo(int $userId): array
    {
        return [
            'fNameEncrypted' => '1',
            'lNameEncrypted' => '1',
            'bDayEncrypted' => '1',
        ];
    }
}
