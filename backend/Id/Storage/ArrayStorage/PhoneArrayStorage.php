<?php

namespace Flow\Id\Storage\ArrayStorage;

use Flow\Id\Storage\Interfaces\PhoneStorageInterface;

class PhoneArrayStorage implements PhoneStorageInterface
{
    /** @var list<array{id: positive-int, userId: positive-int, phone: non-empty-string, phoneHash:non-empty-string, allowAuth:bool}>  */
    public array $usersPhones = [];

    public function getPhonesList(int $userId): array
    {
        $phones = [];
        foreach ($this->usersPhones as $phone) {
            if ($phone['userId'] === $userId) {
                $phones[] = [
                    'id' => $phone['id'],
                    'phone' => $phone['phone'],
                ];
            }
        }

        return $phones;
    }

    public function deletePhone(int $userId, int $itemId): void
    {
        foreach ($this->usersPhones as $index => $phone) {
            if ($phone['userId'] === $userId && $phone['id'] === $itemId) {
                $this->usersPhones = array_slice($this->usersPhones, $index, 1);

                break;
            }
        }
    }

    public function getPhoneItem(int $userId, int $itemId): ?array
    {
        foreach ($this->usersPhones as $phone) {
            if ($phone['userId'] === $userId && $phone['id'] === $itemId) {
                return [
                    'phoneEncrypted' => $phone['phone'],
                    'allowAuth' => (int) $phone['allowAuth'],
                ];
            }
        }

        return null;
    }

    public function insertNewPhone(int $userId, string $phoneEncrypted, string $phoneHash, bool $allowAuth): int
    {
        $phoneId = count($this->usersPhones) + 1;
        $this->usersPhones[] = [
            'id' => $phoneId,
            'userId' => $userId,
            'phone' => $phoneEncrypted,
            'phoneHash' => $phoneHash,
            'allowAuth' => $allowAuth,
        ];

        return $phoneId;
    }

    public function checkPhoneInDatabase(string $phoneHash): bool
    {
        foreach ($this->usersPhones as $phone) {
            if ($phone['phoneHash'] === $phoneHash) {
                return true;
            }
        }

        return false;
    }
}
