<?php

namespace Flow\Id\Storage;

use Symfony\Component\Uid\Uuid;

class UsersArrayStorage implements StorageInterface
{
    /** @var list<array{id:positive-int, uuid:non-empty-string, salt:non-empty-string, iv:non-empty-string, password: non-empty-string, fName: non-empty-string, lName: non-empty-string, bDay: non-empty-string, hash: non-empty-string}> */
    private array $users = [];
    /** @var list<array{id:positive-int, userId: positive-int, emailHash:non-empty-string, emailEncrypted: non-empty-string, allowAuth: bool}> */
    private array $usersEmail = [];
    /** @var list<array{id: positive-int, userId: positive-int, phone: non-empty-string, phoneHash:non-empty-string, allowAuth:bool}>  */
    private array $usersPhones = [];
    /** @var list<array{id: positive-int, userId: positive-int, sessionId: positive-int, authHash: non-empty-string, expiredAt: string}> */
    private array $sessions = [];
    /** @var list<array{id: positive-int, sessionId: positive-int, ip: non-empty-string, ua: non-empty-string, acceptLang: non-empty-string, acceptEncoding: non-empty-string, firstSeenAt: string, lastSeenAt: string}> */
    private array $sessionsMeta = [];

    public function getUserByEmail(string $hashedEmail): ?array
    {
        foreach ($this->usersEmail as $item) {
            if ($item['emailHash'] === $hashedEmail) {
                $user = $this->getUserById($item['userId']);
                if ($user) {
                    return [
                        'userId' => $user['id'],
                        'salt' => $user['salt'],
                        'iv' => $user['iv'],
                        'password' => $user['password'],
                    ];
                }
            }
        }

        return null;
    }

    public function getUserByPhone(string $hashedPhone): ?array
    {
        foreach ($this->usersPhones as $item) {
            if ($item['phoneHash'] === $hashedPhone) {
                $user = $this->getUserById($item['userId']);
                if ($user) {
                    return [
                        'userId' => $user['id'],
                        'salt' => $user['salt'],
                        'iv' => $user['iv'],
                        'password' => $user['password'],
                    ];
                }
            }
        }

        return null;
    }

    public function getUserByUUID(Uuid $uuid): ?array
    {
        foreach ($this->users as $item) {
            if ($uuid->toRfc4122() === $item['uuid']) {
                return [
                    'userId' => $item['id'],
                    'salt' => $item['salt'],
                    'iv' => $item['iv'],
                    'password' => $item['password'],
                ];
            }
        }

        return null;
    }

    public function insertUser(Uuid $uuid, string $password, string $iv, string $salt, string $fNameEncrypted, string $lNameEncrypted, string $bDayEncrypted, string $globalHash): int
    {
        $userId = count($this->users) + 1;
        /** @var non-empty-string $uuid */
        $uuid = $uuid->toRfc4122();
        $this->users[] = [
            'id' => $userId,
            'uuid' => $uuid,
            'password' => $password,
            'iv' => $iv,
            'salt' => $salt,
            'fName' => $fNameEncrypted,
            'lName' => $lNameEncrypted,
            'bDay' => $bDayEncrypted,
            'hash' => $globalHash,
        ];

        return $userId;
    }

    public function insertNewEncryptInfo(int $userId, string $publicKey, string $encryptedPrivateKey): void
    {
        // Implementation not required for testing purposes
    }

    public function insertSession(string $hash, int $userId): void
    {
        $sessionId = count($this->sessions) + 1;
        $this->sessions[] = [
            'id' => $sessionId,
            'userId' => $userId,
            'sessionId' => $sessionId,
            'authHash' => $hash,
            'expiredAt' => date('Y-m-d H:i:s', strtotime('+90 days')),
        ];
    }

    public function checkIssetToken(string $token): ?array
    {
        foreach ($this->sessions as $session) {
            if ($session['authHash'] === $token && strtotime($session['expiredAt']) > time()) {
                return [
                    'userId' => $session['userId'],
                    'lang' => 'en', // Default language for testing
                    'sessionId' => $session['sessionId'],
                ];
            }
        }

        return null;
    }

    public function getEmailList(int $userId): array
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

    public function insertNewEmail(int $userId, string $encryptedEmail, string $emailHash, bool $allowAuth): int
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

    public function editEmailItem(int $userId, int $itemId, string $encryptedEmail, string $emailHash, bool $allowAuth): void
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

    public function getEmailItem(int $userId, int $itemId): ?array
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

    public function deleteEmail(int $userId, int $itemId): void
    {
        foreach ($this->usersEmail as $index => $email) {
            if ($email['userId'] === $userId) {
                $this->usersEmail = array_slice($this->usersEmail, $index, 1);

                break;
            }
        }
    }

    public function getPhonesList(int $userId): array
    {
        $phones = [];
        foreach ($this->usersPhones as $phone) {
            if ($phone['userId'] === $userId) {
                $phones[] = [
                    'id' => $phone['id'],
                    'phone' => $phone['phone'],
                ];
            }
        }

        return $phones;
    }

    public function deletePhone(int $userId, int $itemId): void
    {
        foreach ($this->usersPhones as $index => $phone) {
            if ($phone['userId'] === $userId && $phone['id'] === $itemId) {
                $this->usersEmail = array_slice($this->usersEmail, $index, 1);

                break;
            }
        }
    }

    public function getPhoneItem(int $userId, int $itemId): ?array
    {
        foreach ($this->usersPhones as $phone) {
            if ($phone['userId'] === $userId && $phone['id'] === $itemId) {
                return [
                    'phoneEncrypted' => $phone['phone'],
                    'allowAuth' => (int) $phone['allowAuth'],
                ];
            }
        }

        return null;
    }

    public function insertNewPhone(int $userId, string $phoneEncrypted, string $phoneHash, bool $allowAuth): int
    {
        $phoneId = count($this->usersPhones) + 1;
        $this->usersPhones[] = [
            'id' => $phoneId,
            'userId' => $userId,
            'phone' => $phoneEncrypted,
            'phoneHash' => $phoneHash,
            'allowAuth' => $allowAuth,
        ];

        return $phoneId;
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

    public function checkPhoneInDatabase(string $phoneHash): bool
    {
        foreach ($this->usersPhones as $phone) {
            if ($phone['phoneHash'] === $phoneHash) {
                return true;
            }
        }

        return false;
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

    public function checkIssetSessionMetaInfo(string $session, string $encryptedIp, string $encryptedUa, string $encryptedAE, string $encryptedAL): ?int
    {
        foreach ($this->sessionsMeta as $index => $meta) {
            if ($meta['sessionId'] === (int) $session &&
                $meta['ip'] === $encryptedIp &&
                $meta['ua'] === $encryptedUa &&
                $meta['acceptEncoding'] === $encryptedAE &&
                $meta['acceptLang'] === $encryptedAL) {
                return $index + 1;
            }
        }

        return null;
    }

    public function insertSessionMeta(int $sessionId, string $encryptedIp, string $encryptedUa, string $encryptedAE, string $encryptedAL, string $encryptedLastSeenAt): void
    {
        $id = count($this->sessionsMeta) + 1;
        $this->sessionsMeta[] = [
            'id' => $id,
            'sessionId' => $sessionId,
            'ip' => $encryptedIp,
            'ua' => $encryptedUa,
            'acceptLang' => $encryptedAL,
            'acceptEncoding' => $encryptedAE,
            'firstSeenAt' => $encryptedLastSeenAt,
            'lastSeenAt' => $encryptedLastSeenAt,
        ];
    }

    public function updateLastSeenSessionMeta(int $sessionMetaInfoId, string $encryptedLastSeenAt): void
    {
        foreach ($this->sessionsMeta as &$session) {
            if ($session['id'] === $sessionMetaInfoId) {
                $session['lastSeenAt'] = $encryptedLastSeenAt;

                return;
            }
        }
    }

    public function getBasicInfo(int $userId): ?array
    {
        $user = $this->getUserById($userId);
        if ($user) {
            return [
                'fNameEncrypted' => $user['fName'],
                'lNameEncrypted' => $user['lName'],
                'bDayEncrypted' => $user['bDay'],
            ];
        }

        return null;
    }

    /**
     * @param int $userId
     * @return array{id:positive-int, uuid:non-empty-string, salt:non-empty-string, iv:non-empty-string, password: non-empty-string, fName: non-empty-string, lName: non-empty-string, bDay: non-empty-string, hash: non-empty-string}|null
     */
    private function getUserById(int $userId): ?array
    {
        foreach ($this->users as $user) {
            if ($user['id'] === $userId) {
                return $user;
            }
        }

        return null;
    }
}
