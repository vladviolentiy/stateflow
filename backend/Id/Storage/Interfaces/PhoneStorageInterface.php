<?php

namespace Flow\Id\Storage\Interfaces;

use Flow\Core\Exceptions\DatabaseException;

interface PhoneStorageInterface
{
    /**
     * @param non-empty-string $phoneHash
     * @return bool
     */
    public function checkPhoneInDatabase(string $phoneHash): bool;

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
}
