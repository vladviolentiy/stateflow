<?php

namespace Flow\Id\Storage\Interfaces;

interface EmailStorageInterface
{
    /**
     * @param positive-int $userId
     * @return list<array{id:positive-int,email:string}>
     */
    public function getByUserId(int $userId): array;

    /**
     * @param positive-int $userId
     * @param non-empty-string $encryptedEmail
     * @param non-empty-string $emailHash
     * @param bool $allowAuth
     * @return int
     */
    public function insertNew(int $userId, string $encryptedEmail, string $emailHash, bool $allowAuth): int;

    /**
     * @param positive-int $userId
     * @param positive-int $itemId
     * @param non-empty-string $encryptedEmail
     * @param non-empty-string $emailHash
     * @param bool $allowAuth
     * @return void
     */
    public function editItem(int $userId, int $itemId, string $encryptedEmail, string $emailHash, bool $allowAuth): void;

    /**
     * @param positive-int $userId
     * @param positive-int $itemId
     * @return array{emailEncrypted:string,allowAuth:int}|null
     */
    public function getItemById(int $userId, int $itemId): ?array;

    /**
     * @param non-empty-string $hash
     * @return array{userId:positive-int}|null
     */
    public function getItemByHash(string $hash): ?array;

    /**
     * @param positive-int $userId
     * @param positive-int $itemId
     * @return void
     */
    public function deleteItem(int $userId, int $itemId): void;

    /**
     * @param non-empty-string $emailHash
     * @return bool
     */
    public function checkEmailInDatabase(string $emailHash): bool;
}
