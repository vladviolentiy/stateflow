<?php

namespace Flow\Id\Storage;

use Flow\Id\Storage\Interfaces\EmailStorageInterface;
use VladViolentiy\VivaFramework\Databases\MysqliV2;

final class EmailStorage extends MysqliV2 implements EmailStorageInterface
{
    public function __construct()
    {
        $this->setDb(DatabaseSingleton::getInstance());
    }

    public function getByUserId(int $userId): array
    {
        /** @var list<array{id:positive-int,email:string}> $data */
        $data = $this->executeQuery('SELECT id, emailEncrypted as email FROM usersEmails WHERE userId=? and deleted=false', [$userId])->fetch_all(MYSQLI_ASSOC);

        return $data;
    }

    public function editItem(int $userId, int $itemId, string $encryptedEmail, string $emailHash, bool $allowAuth): void
    {
        $this->executeQueryBool('UPDATE usersEmails SET emailEncrypted=?, emailHash=UNHEX(?),allowAuth=? WHERE id=? and userId=?', [$encryptedEmail, $emailHash, chr($allowAuth ? 1 : 0), $itemId, $userId]);
    }

    public function insertNew(int $userId, string $encryptedEmail, string $emailHash, bool $allowAuth): int
    {
        $this->executeQueryBool('INSERT INTO usersEmails(userId, emailHash, emailEncrypted,allowAuth) VALUES (?,unhex(?),?,?)', [$userId, $emailHash, $encryptedEmail, chr($allowAuth ? 1 : 0)]);

        return $this->insertId();
    }

    public function getItemById(int $userId, int $itemId): ?array
    {
        /** @var array{emailEncrypted:non-empty-string,allowAuth:int<0,1>}|null $info */
        $info = $this->executeQuery('SELECT emailEncrypted, allowAuth FROM usersEmails WHERE id=? and userId=?', [$itemId, $userId])->fetch_array(MYSQLI_ASSOC);

        return $info;
    }

    public function deleteItem(int $userId, int $itemId): void
    {
        $this->executeQueryBool('UPDATE usersEmails SET deleted=true WHERE id=? and userId=?', [$itemId, $userId]);
    }

    public function checkEmailInDatabase(string $emailHash): bool
    {
        /** @var array{count:int} $data */
        $data = $this->executeQuery('SELECT COUNT(*) as count FROM usersEmails WHERE emailHash=?', [$emailHash])->fetch_array(MYSQLI_ASSOC);

        return $data['count'] > 0;
    }

    public function getItemByHash(string $hash): ?array
    {
        /** @var array{userId:positive-int}|null $data */
        $data = $this->executeQuery('SELECT userId FROM usersEmails WHERE emailHash=? and deleted=false', [$hash])->fetch_array(MYSQLI_ASSOC);

        return $data;
    }
}
