<?php

namespace Flow\Id\Storage\Interfaces;

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
     * @param non-empty-string $encryptedIp
     * @param non-empty-string $encryptedUa
     * @param non-empty-string $encryptedAE
     * @param non-empty-string $encryptedAL
     * @param non-empty-string $encryptedLastSeenAt
     * @return void
     */
    public function insertSessionMeta(
        int $sessionId,
        string $encryptedIp,
        string $encryptedUa,
        string $encryptedAE,
        string $encryptedAL,
        string $encryptedLastSeenAt,
    ): void;

    /**
     * @param positive-int $sessionMetaInfoId
     * @param non-empty-string $encryptedLastSeenAt
     * @return void
     */
    public function updateLastSeenSessionMeta(
        int $sessionMetaInfoId,
        string $encryptedLastSeenAt,
    ): void;

    /**
     * @param non-empty-string $session
     * @param non-empty-string $encryptedIp
     * @param non-empty-string $encryptedUa
     * @param non-empty-string $encryptedAE
     * @param non-empty-string $encryptedAL
     * @return positive-int|null
     */
    public function checkIssetSessionMetaInfo(
        string $session,
        string $encryptedIp,
        string $encryptedUa,
        string $encryptedAE,
        string $encryptedAL,
    ): ?int;

    /**
     * @param non-empty-string $token
     * @return array{userId:positive-int,lang:non-empty-string,sessionId:positive-int}|null
     */
    public function checkIssetToken(string $token): ?array;
}
