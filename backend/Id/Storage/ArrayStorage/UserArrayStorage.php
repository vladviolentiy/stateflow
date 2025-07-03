<?php

namespace Flow\Id\Storage\ArrayStorage;

use Flow\Id\ValueObject\EncryptedData;
use Flow\Id\ValueObject\Password;
use Flow\Id\ValueObject\PrivateKey;
use Flow\Id\ValueObject\RsaPublicKey;
use Flow\Id\Storage\Interfaces\UserStorageInterface;
use Symfony\Component\Uid\Uuid;

class UserArrayStorage implements UserStorageInterface
{
    /** @var list<array{id:positive-int, uuid:non-empty-string, salt:non-empty-string, iv:non-empty-string, password: non-empty-string, fName: non-empty-string, lName: non-empty-string, bDay: non-empty-string, hash: non-empty-string}> */
    private array $users = [];

    /** @var list<array{id: positive-int, userId: positive-int, type: 'default', encryptedPrivateKey: non-empty-string, publicKey: non-empty-string}>  */
    private array $encryptedInfo = [];

    public function __construct(
        private readonly EmailArrayStorage $emailStorage,
        private readonly PhoneArrayStorage $phoneStorage,
    ) {}

    public function getUserByEmail(string $hashedEmail): ?array
    {
        foreach ($this->emailStorage->usersEmail as $item) {
            if ($item['emailHash'] === $hashedEmail) {
                $user = $this->getUserById($item['userId']);
                if ($user) {
                    return [
                        'userId' => $user['id'],
                        'salt' => $user['salt'],
                        'iv' => $user['iv'],
                        'password' => $user['password'],
                    ];
                }
            }
        }

        return null;
    }

    public function getUserByPhone(string $hashedPhone): ?array
    {
        foreach ($this->phoneStorage->usersPhones as $item) {
            if ($item['phoneHash'] === $hashedPhone) {
                $user = $this->getUserById($item['userId']);
                if ($user) {
                    return [
                        'userId' => $user['id'],
                        'salt' => $user['salt'],
                        'iv' => $user['iv'],
                        'password' => $user['password'],
                    ];
                }
            }
        }

        return null;
    }

    public function getUserByUUID(Uuid $uuid): ?array
    {
        foreach ($this->users as $item) {
            if ($uuid->toRfc4122() === $item['uuid']) {
                return [
                    'userId' => $item['id'],
                    'salt' => $item['salt'],
                    'iv' => $item['iv'],
                    'password' => $item['password'],
                ];
            }
        }

        return null;
    }

    public function insertUser(Uuid $uuid, Password $password, string $iv, string $salt, EncryptedData $fNameEncrypted, EncryptedData $lNameEncrypted, EncryptedData $bDayEncrypted, string $globalHash): int
    {
        $userId = count($this->users) + 1;
        /** @var non-empty-string $uuidString */
        $uuidString = $uuid->toRfc4122();
        $this->users[] = [
            'id' => $userId,
            'uuid' => $uuidString,
            'password' => $password->value,
            'iv' => $iv,
            'salt' => $salt,
            'fName' => $fNameEncrypted->value,
            'lName' => $lNameEncrypted->value,
            'bDay' => $bDayEncrypted->value,
            'hash' => $globalHash,
        ];

        return $userId;
    }

    public function insertNewEncryptInfo(int $userId, RsaPublicKey $publicKey, PrivateKey $encryptedPrivateKey): void
    {
        $itemId = count($this->encryptedInfo) + 1;
        $this->encryptedInfo[] = [
            'id' => $itemId,
            'userId' => $userId,
            'type' => 'default',
            'publicKey' => $publicKey->value,
            'encryptedPrivateKey' => $encryptedPrivateKey->value,
        ];
    }

    public function getBasicInfo(int $userId): ?array
    {
        $user = $this->getUserById($userId);
        if ($user) {
            return [
                'fNameEncrypted' => $user['fName'],
                'lNameEncrypted' => $user['lName'],
                'bDayEncrypted' => $user['bDay'],
            ];
        }

        return null;
    }

    /**
     * @param int $userId
     * @return array{id:positive-int, uuid:non-empty-string, salt:non-empty-string, iv:non-empty-string, password: non-empty-string, fName: non-empty-string, lName: non-empty-string, bDay: non-empty-string, hash: non-empty-string}|null
     */
    private function getUserById(int $userId): ?array
    {
        foreach ($this->users as $user) {
            if ($user['id'] === $userId) {
                return $user;
            }
        }

        return null;
    }

    public function updateUserPrivateKey(int $userId, PrivateKey $privateKey): void
    {
        foreach ($this->encryptedInfo as $item) {
            if ($item['userId'] === $userId) {
                $item['privateKey'] = $privateKey;
            }
        }
    }

    public function updatePassword(int $userId, Password $newPassword): void
    {
        foreach ($this->users as $user) {
            if ($user['id'] === $userId) {
                $user['password'] = $newPassword->value;
            }
        }
    }

    public function beginTransaction(): void {}

    public function commit(): void {}

    public function rollBack(): void {}
}
