<?php

namespace Flow\Id\Storage\ArrayStorage;

use Flow\Id\Storage\Interfaces\SessionStorageInterface;
use Flow\Id\ValueObject\EncryptedData;

class SessionArrayStorage implements SessionStorageInterface
{
    /** @var list<array{id: positive-int, userId: positive-int, authHash: non-empty-string, expiredAt: string}> */
    private array $sessions = [];
    /** @var list<array{id: positive-int, sessionId: positive-int, ip: non-empty-string, ua: non-empty-string, acceptLang: non-empty-string, acceptEncoding: non-empty-string, firstSeenAt: string, lastSeenAt: string}> */
    private array $sessionsMeta = [];

    public function insertSession(string $hash, int $userId): void
    {
        $sessionId = count($this->sessions) + 1;
        $this->sessions[] = [
            'id' => $sessionId,
            'userId' => $userId,
            'authHash' => $hash,
            'expiredAt' => date('Y-m-d H:i:s', strtotime('+90 days')),
        ];
    }

    public function getSessionsForUser(int $userId): array
    {
        $sessions = [];
        foreach ($this->sessions as $session) {
            if ($session['userId'] === $userId && strtotime($session['expiredAt']) > time()) {
                $sessions[] = [
                    'authHash' => $session['authHash'],
                    'uas' => 'ua', // Default for testing
                    'ips' => 'ip', // Default for testing
                    'createdAt' => (new \DateTimeImmutable($session['expiredAt']))->format('d.m.Y H:i:s'),
                ];
            }
        }

        return $sessions;
    }

    public function killSession(int $userId, string $hash): void
    {
        foreach ($this->sessions as &$session) {
            if ($session['userId'] === $userId && $session['authHash'] === $hash) {
                $session['expiredAt'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

                break;
            }
        }
    }

    public function checkIssetSessionMetaInfo(string $session, EncryptedData $encryptedIp, EncryptedData $encryptedUa, EncryptedData $encryptedAE, EncryptedData $encryptedAL): ?int
    {
        $user = $this->checkIssetToken($session);
        if ($user === null) {
            return null;
        }
        foreach ($this->sessionsMeta as $index => $meta) {
            if ($meta['sessionId'] === $user['sessionId'] &&
                $meta['ip'] === $encryptedIp->value &&
                $meta['ua'] === $encryptedUa->value &&
                $meta['acceptEncoding'] === $encryptedAE->value &&
                $meta['acceptLang'] === $encryptedAL->value) {
                return $index + 1;
            }
        }

        return null;
    }

    public function insertSessionMeta(int $sessionId, EncryptedData $encryptedIp, EncryptedData $encryptedUa, EncryptedData $encryptedAE, EncryptedData $encryptedAL, EncryptedData $encryptedLastSeenAt): void
    {
        $id = count($this->sessionsMeta) + 1;
        $this->sessionsMeta[] = [
            'id' => $id,
            'sessionId' => $sessionId,
            'ip' => $encryptedIp->value,
            'ua' => $encryptedUa->value,
            'acceptLang' => $encryptedAL->value,
            'acceptEncoding' => $encryptedAE->value,
            'firstSeenAt' => $encryptedLastSeenAt->value,
            'lastSeenAt' => $encryptedLastSeenAt->value,
        ];
    }

    public function updateLastSeenSessionMeta(int $sessionMetaInfoId, EncryptedData $encryptedLastSeenAt): void
    {
        foreach ($this->sessionsMeta as &$session) {
            if ($session['id'] === $sessionMetaInfoId) {
                $session['lastSeenAt'] = $encryptedLastSeenAt->value;

                return;
            }
        }
    }

    public function checkIssetToken(string $token): ?array
    {
        foreach ($this->sessions as $session) {
            if ($session['authHash'] === $token) {
                return [
                    'userId' => $session['userId'],
                    'lang' => 'ru',
                    'sessionId' => $session['id'],
                ];
            }
        }

        return null;
    }
}
