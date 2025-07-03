<?php

namespace Flow\Id\Storage\Interfaces;

use Flow\Id\ValueObject\EncryptedData;

interface SessionStorageInterface
{
    /**
     * @param non-empty-string $hash
     * @param positive-int $userId
     * @return void
     */
    public function insertSession(string $hash, int $userId): void;

    /**
     * @param positive-int $userId
     * @return list<array{authHash:non-empty-string,uas:non-empty-string,ips:non-empty-string,createdAt:non-empty-string}>
     * @throws \VladViolentiy\VivaFramework\Exceptions\DatabaseException
     */
    public function getSessionsForUser(int $userId): array;

    /**
     * @param positive-int $userId
     * @param non-empty-string $hash
     * @return void
     */
    public function killSession(int $userId, string $hash): void;

    /**
     * @param positive-int $sessionId
     * @param EncryptedData $encryptedIp
     * @param EncryptedData $encryptedUa
     * @param EncryptedData $encryptedAE
     * @param EncryptedData $encryptedAL
     * @param EncryptedData $encryptedLastSeenAt
     * @return void
     */
    public function insertSessionMeta(
        int $sessionId,
        EncryptedData $encryptedIp,
        EncryptedData $encryptedUa,
        EncryptedData $encryptedAE,
        EncryptedData $encryptedAL,
        EncryptedData $encryptedLastSeenAt,
    ): void;

    /**
     * @param positive-int $sessionMetaInfoId
     * @param EncryptedData $encryptedLastSeenAt
     * @return void
     */
    public function updateLastSeenSessionMeta(
        int $sessionMetaInfoId,
        EncryptedData $encryptedLastSeenAt,
    ): void;

    /**
     * @param non-empty-string $session
     * @param EncryptedData $encryptedIp
     * @param EncryptedData $encryptedUa
     * @param EncryptedData $encryptedAE
     * @param EncryptedData $encryptedAL
     * @return positive-int|null
     */
    public function checkIssetSessionMetaInfo(
        string $session,
        EncryptedData $encryptedIp,
        EncryptedData $encryptedUa,
        EncryptedData $encryptedAE,
        EncryptedData $encryptedAL,
    ): ?int;

    /**
     * @param non-empty-string $token
     * @return array{userId:positive-int,lang:non-empty-string,sessionId:positive-int}|null
     */
    public function checkIssetToken(string $token): ?array;
}
