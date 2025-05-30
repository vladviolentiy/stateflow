<?php

namespace Flow\Id\Storage;

use Flow\Core\DatabaseConnectionFactory;
use Flow\Id\Storage\Interfaces\PhoneStorageInterface;
use VladViolentiy\VivaFramework\Databases\MysqliV2;

final class PhoneStorage extends MysqliV2 implements PhoneStorageInterface
{
    public function __construct()
    {
        $this->setDb(DatabaseConnectionFactory::getInstance());
    }

    public function getPhonesList(int $userId): array
    {
        /** @var list<array{id:int,phone:string}> $data */
        $data = $this->executeQuery('SELECT id, phoneEncrypted as phone FROM usersPhones WHERE userId=? and deleted=false', [$userId])->fetch_all(MYSQLI_ASSOC);

        return $data;
    }

    public function deletePhone(int $userId, int $itemId): void
    {
        $this->executeQueryBool('UPDATE usersPhones SET deleted=true WHERE id=? and userId=?', [$itemId, $userId]);
    }

    public function getPhoneItem(int $userId, int $itemId): ?array
    {
        /** @var array{phoneEncrypted:string,allowAuth:int}|null $info */
        $info = $this->executeQuery('SELECT phoneEncrypted,allowAuth FROM usersPhones WHERE id=? and userId=?', [$itemId, $userId])->fetch_array(MYSQLI_ASSOC);

        return $info;
    }

    public function insertNewPhone(int $userId, string $phoneEncrypted, string $phoneHash, bool $allowAuth): int
    {
        /** @var positive-int $id */
        $id = $this->executeQueryBool('INSERT INTO usersPhones(userId, phoneHash, phoneEncrypted, allowAuth) VALUES (?,unhex(?),?,?)', [$userId, $phoneHash, $phoneEncrypted, chr($allowAuth ? 1 : 0)]);

        return $id;
    }

    public function checkPhoneInDatabase(string $phoneHash): bool
    {
        /** @var array{count:int} $data */
        $data = $this->executeQuery('SELECT COUNT(*) as count FROM usersPhones WHERE phoneHash=?', [$phoneHash])->fetch_array(MYSQLI_ASSOC);

        return $data['count'] > 0;
    }
}
