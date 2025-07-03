<?php

namespace Flow\Id\Storage;

use Flow\Id\Storage\Interfaces\SessionStorageInterface;
use Flow\Id\ValueObject\EncryptedData;
use mysqli;
use VladViolentiy\VivaFramework\Databases\MysqliV2;

final class SessionStorage extends MysqliV2 implements SessionStorageInterface
{
    public function __construct(mysqli $connection)
    {
        $this->setDb($connection);
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

    public function insertSessionMeta(
        int $sessionId,
        EncryptedData $encryptedIp,
        EncryptedData $encryptedUa,
        EncryptedData $encryptedAE,
        EncryptedData $encryptedAL,
        EncryptedData $encryptedLastSeenAt,
    ): void {
        $this->executeQueryBool('INSERT INTO 
    sessionsMeta(sessionId, ip, ua, acceptLang, acceptEncoding, firstSeenAt, lastSeenAt) 
VALUES (?,?,?,?,?,?,?)', [$sessionId, $encryptedIp->value, $encryptedUa->value, $encryptedAL->value, $encryptedAE->value, $encryptedLastSeenAt->value, $encryptedLastSeenAt->value]);
    }

    public function updateLastSeenSessionMeta(int $sessionMetaInfoId, EncryptedData $encryptedLastSeenAt): void
    {
        $this->executeQueryBool('UPDATE sessionsMeta SET lastSeenAt=? where id=?', [$encryptedLastSeenAt->value, $sessionMetaInfoId]);
    }

    public function insertSession(string $hash, int $userId): void
    {
        $this->executeQueryBool('INSERT INTO sessions(authHash, userId, expiredAt) VALUES (UNHEX(?),?,DATE_ADD(now(),INTERVAL 90 DAY ))', [$hash, $userId]);
    }

    public function checkIssetSessionMetaInfo(
        string $session,
        EncryptedData $encryptedIp,
        EncryptedData $encryptedUa,
        EncryptedData $encryptedAE,
        EncryptedData $encryptedAL,
    ): ?int {
        /** @var array{id:positive-int}|null $i */
        $i = $this->executeQuery('SELECT sessionsMeta.id
FROM sessionsMeta 
    JOIN sessions ON sessionsMeta.sessionId=sessions.id 
WHERE authHash=unhex(?) and ip=? and ua=? and acceptEncoding=? and acceptLang=?', [$session, $encryptedIp->value, $encryptedUa->value, $encryptedAE->value, $encryptedAL->value])->fetch_array(MYSQLI_ASSOC);
        if ($i === null) {
            return null;
        }

        return $i['id'];
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
}
