<?php

namespace Flow\Id\Storage\ArrayStorage;

use Flow\Id\Storage\Interfaces\EmailStorageInterface;

class EmailArrayStorage implements EmailStorageInterface
{
    /** @var list<array{id:positive-int, userId: positive-int, emailHash:non-empty-string, emailEncrypted: non-empty-string, allowAuth: bool}> */
    public array $usersEmail = [];

    public function getByUserId(int $userId): array
    {
        $emails = [];
        foreach ($this->usersEmail as $email) {
            if ($email['userId'] === $userId) {
                $emails[] = [
                    'id' => count($emails) + 1,
                    'email' => $email['emailEncrypted'], // For testing purposes
                ];
            }
        }

        return $emails;
    }

    public function insertNew(int $userId, string $encryptedEmail, string $emailHash, bool $allowAuth): int
    {
        $emailId = count($this->usersEmail) + 1;
        $this->usersEmail[] = [
            'id' => $emailId,
            'userId' => $userId,
            'emailHash' => $emailHash,
            'emailEncrypted' => $encryptedEmail,
            'allowAuth' => $allowAuth,
        ];

        return $emailId;
    }

    public function editItem(int $userId, int $itemId, string $encryptedEmail, string $emailHash, bool $allowAuth): void
    {
        foreach ($this->usersEmail as &$email) {
            if ($email['userId'] === $userId && $email['emailHash'] === $encryptedEmail) {
                $email['emailHash'] = $emailHash;
                $email['emailEncrypted'] = $encryptedEmail;
                $email['allowAuth'] = $allowAuth;

                break;
            }
        }
    }

    public function getItemById(int $userId, int $itemId): ?array
    {
        foreach ($this->usersEmail as $email) {
            if ($email['userId'] === $userId) {
                return [
                    'emailEncrypted' => $email['emailEncrypted'],
                    'allowAuth' => (int) $email['allowAuth'],
                ];
            }
        }

        return null;
    }

    public function deleteItem(int $userId, int $itemId): void
    {
        foreach ($this->usersEmail as $index => $email) {
            if ($email['userId'] === $userId) {
                $this->usersEmail = array_slice($this->usersEmail, $index, 1);

                break;
            }
        }
    }

    public function checkEmailInDatabase(string $emailHash): bool
    {
        foreach ($this->usersEmail as $email) {
            if ($email['emailHash'] === $emailHash) {
                return true;
            }
        }

        return false;
    }

    public function getItemByHash(string $hash): ?array
    {
        foreach ($this->usersEmail as $email) {
            if ($email['emailHash'] === $hash) {
                return [
                    'userId' => $email['userId'],
                ];
            }
        }

        return null;
    }
}
