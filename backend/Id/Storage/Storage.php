<?php

namespace Flow\Id\Storage;

use Flow\Core\Database;
use Flow\Core\Enums\ServicesEnum;
use Flow\Id\Storage\Migrations\Migration;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Databases\MigrationV2\MysqlMigrationManager;
use VladViolentiy\VivaFramework\Databases\MysqliV2;
use mysqli;

class Storage extends MysqliV2 implements StorageInterface
{
    public function __construct(?mysqli $connection = null)
    {
        if ($connection === null) {
            $connection = Database::createConnection(ServicesEnum::Id);
        }
        $this->setDb($connection);
        (new MysqlMigrationManager($connection))->migrate(Migration::$list);

    }

    public function getUserByEmail(string $hashedEmail): ?array
    {
        /** @var array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null $info */
        $info = $this->executeQuery('SELECT u.id as userId, salt, iv
FROM usersEmails 
    JOIN users u on usersEmails.userId = u.id
WHERE emailHash=unhex(?) and allowAuth=true and deleted=false', [$hashedEmail])->fetch_array(MYSQLI_ASSOC);
        if ($info === null) {
            return null;
        }

        return $info;
    }

    public function getUserByUUID(Uuid $uuid): ?array
    {
        /** @var array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null $info */
        $info = $this->executeQuery('SELECT id as userId, salt, iv, password
FROM users
WHERE uuid=unhex(?)', [$uuid->toBinary()])->fetch_array(MYSQLI_ASSOC);
        if ($info === null) {
            return null;
        }

        return $info;
    }

    public function getUserByPhone(string $hashedPhone): ?array
    {
        /** @var array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}|null $info */
        $info = $this->executeQuery('SELECT users.id as userId,salt,iv, password
FROM users
    JOIN usersPhones uP on users.id = uP.userId
WHERE phoneHash=unhex(?)', [$hashedPhone])->fetch_array(MYSQLI_ASSOC);
        if ($info === null) {
            return null;
        }

        return $info;
    }

    public function insertUser(Uuid $uuid, string $password, string $iv, string $salt, string $fNameEncrypted, string $lNameEncrypted, string $bDayEncrypted, string $globalHash): int
    {
        $this->executeQueryBool(
            'INSERT INTO users(uuid, password, iv, salt, fNameEncrypted, lNameEncrypted, bDayEncrypted, globalHash) VALUES(?,?,?,?,?,?,?,unhex(?))',
            [$uuid->toBinary(), $password, $iv, $salt, $fNameEncrypted, $lNameEncrypted, $bDayEncrypted, $globalHash],
        );
        /** @var positive-int $insId */
        $insId = $this->insertId();

        return $insId;
    }

    public function insertNewEncryptInfo(int $userId, string $publicKey, string $encryptedPrivateKey): void
    {
        $this->executeQueryBool(
            'INSERT INTO usersEncryptInfo(userId, publicKey, encryptedPrivateKey) VALUES(?,?,?)',
            [$userId, $publicKey, $encryptedPrivateKey],
        );
    }

    public function checkIssetToken(string $token): ?array
    {
        /** @var array{userId:positive-int,lang:non-empty-string,sessionId:positive-int}|null $info */
        $info = $this->executeQuery('SELECT userId, u.defaultLang as lang, sessions.id as sessionId 
FROM sessions 
    JOIN users u on u.id = sessions.userId 
WHERE authHash=unhex(?)', [$token])->fetch_array(MYSQLI_ASSOC);
        if ($info === null) {
            return null;
        }

        return $info;
    }

    public function insertSession(string $hash, int $userId): void
    {
        $this->executeQueryBool('INSERT INTO sessions(authHash, userId, expiredAt) VALUES (UNHEX(?),?,DATE_ADD(now(),INTERVAL 90 DAY ))', [$hash, $userId]);
    }

    public function getEmailList(int $userId): array
    {
        /** @var list<array{id:positive-int,email:string}> $data */
        $data = $this->executeQuery('SELECT id, emailEncrypted as email FROM usersEmails WHERE userId=? and deleted=false', [$userId])->fetch_all(MYSQLI_ASSOC);

        return $data;
    }

    public function editEmailItem(int $userId, int $itemId, string $encryptedEmail, string $emailHash, bool $allowAuth): void
    {
        $this->executeQueryBool('UPDATE usersEmails SET emailEncrypted=?, emailHash=UNHEX(?),allowAuth=? WHERE id=? and userId=?', [$encryptedEmail, $emailHash, chr($allowAuth ? 1 : 0), $itemId, $userId]);
    }

    public function insertNewEmail(int $userId, string $encryptedEmail, string $emailHash, bool $allowAuth): int
    {
        $this->executeQueryBool('INSERT INTO usersEmails(userId, emailHash, emailEncrypted,allowAuth) VALUES (?,unhex(?),?,?)', [$userId, $emailHash, $encryptedEmail, chr($allowAuth ? 1 : 0)]);

        return $this->insertId();
    }

    public function getEmailItem(int $userId, int $itemId): ?array
    {
        /** @var array{emailEncrypted:string,allowAuth:int}|null $info */
        $info = $this->executeQuery('SELECT emailEncrypted, allowAuth FROM usersEmails WHERE id=? and userId=?', [$itemId, $userId])->fetch_array(MYSQLI_ASSOC);

        return $info;
    }

    public function deleteEmail(int $userId, int $itemId): void
    {
        $this->executeQueryBool('UPDATE usersEmails SET deleted=true WHERE id=? and userId=?', [$itemId, $userId]);
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

    public function checkEmailInDatabase(string $emailHash): bool
    {
        /** @var array{count:int} $i */
        $i = $this->executeQuery('SELECT count(*) as count 
FROM usersEmails 
WHERE emailHash=? and deleted=false', [$emailHash])->fetch_array(MYSQLI_ASSOC);

        return $i['count'] > 0;
    }

    public function getSessionsForUser(int $userId): array
    {
        /** @var list<array{authHash:non-empty-string,uas:non-empty-string,ips:non-empty-string,createdAt:non-empty-string}> $i */
        $i = $this->executeQuery("SELECT 
    hex(authHash) as authHash,
    DATE_FORMAT(createdAt,'%d.%m.%Y %H:%i') as createdAt,
    group_concat(sM.ua) as uas,
    group_concat(sM.ip) as ips
FROM sessions 
    JOIN sessionsMeta sM on sessions.id = sM.sessionId
WHERE userId=? and expiredAt>now() 
GROUP BY sessions.id", [$userId])->fetch_all(MYSQLI_ASSOC);

        return $i;
    }

    public function killSession(int $userId, string $hash): void
    {
        $this->executeQueryBool('UPDATE sessions SET expiredAt=now() WHERE userId=? and authHash=unhex(?)', [$userId, $hash]);
    }

    public function checkIssetSessionMetaInfo(
        string $session,
        string $encryptedIp,
        string $encryptedUa,
        string $encryptedAE,
        string $encryptedAL,
    ): ?int {
        /** @var array{id:positive-int}|null $i */
        $i = $this->executeQuery('SELECT sessionsMeta.id
FROM sessionsMeta 
    JOIN sessions ON sessionsMeta.sessionId=sessions.id 
WHERE authHash=unhex(?) and ip=? and ua=? and acceptEncoding=? and acceptLang=?', [$session, $encryptedIp, $encryptedUa, $encryptedAE, $encryptedAL])->fetch_array(MYSQLI_ASSOC);
        if ($i === null) {
            return null;
        }

        return $i['id'];
    }

    public function insertSessionMeta(
        int $sessionId,
        string $encryptedIp,
        string $encryptedUa,
        string $encryptedAE,
        string $encryptedAL,
        string $encryptedLastSeenAt,
    ): void {
        $this->executeQueryBool('INSERT INTO 
    sessionsMeta(sessionId, ip, ua, acceptLang, acceptEncoding, firstSeenAt, lastSeenAt) 
VALUES (?,?,?,?,?,?,?)', [$sessionId, $encryptedIp, $encryptedUa, $encryptedAL, $encryptedAE, $encryptedLastSeenAt, $encryptedLastSeenAt]);
    }

    public function updateLastSeenSessionMeta(int $sessionMetaInfoId, string $encryptedLastSeenAt): void
    {
        $this->executeQueryBool('UPDATE sessionsMeta SET lastSeenAt=? where id=?', [$encryptedLastSeenAt, $sessionMetaInfoId]);
    }

    public function getBasicInfo(int $userId): ?array
    {
        /** @var array{fNameEncrypted:non-empty-string,lNameEncrypted:non-empty-string,bDayEncrypted:non-empty-string}|null $i */
        $i = $this->executeQuery('SELECT fNameEncrypted, lNameEncrypted, bDayEncrypted FROM users WHERE id=?', [$userId])->fetch_array(MYSQLI_ASSOC);

        return $i;
    }
}
