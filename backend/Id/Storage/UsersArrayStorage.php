<?php

namespace Flow\Id\Storage;

use Ramsey\Uuid\UuidInterface;

class UsersArrayStorage implements StorageInterface
{
    /**
     * @var list<array{id:int,uuid:string,salt:string,iv:string}>
     */
    private array $users = [];
//    /**
//     * @var array{userId:int,private:string,public:string}
//     */
//    private array $keysStorage;
    public function getUserByEmail(string $hashedEmail): ?array
    {
        return null;
    }

    public function getUserByPhone(string $hashedPhone): ?array
    {
        return null;
    }

    public function getUserByUUID(UuidInterface $uuid): ?array
    {
        foreach ($this->users as $key=>$item) {
            if($uuid->toString()===$item['uuid']){
                return [
                    "userId"=>$item['id'],
                    "salt"=>$item['salt'],
                    "iv"=>$item['iv']
                ];
            }
        }
        return null;
    }

    public function checkIssetUUID(string $uuid): bool
    {
        return false;
    }

    public function addNewUser(UuidInterface $uuid, string $password, string $iv, string $salt, string $fNameEncrypted, string $lNameEncrypted, string $bDayEncrypted, string $globalHash): int
    {
        $userId = count($this->users)+1;
        $this->users[] = [
            "id"=>$userId,
            "uuid"=>$uuid->toString(),
            "password"=>$password,
            "iv"=>$iv,
            "salt"=>$salt,
            "fName"=>$fNameEncrypted,
            "lName"=>$lNameEncrypted,
            "bDay"=>$bDayEncrypted,
            "hash"=>$globalHash
        ];
        return $userId;
    }

    public function insertNewEncryptInfo(int $userId, string $publicKey, string $encryptedPrivateKey): void
    {
        //TODO: Phpstan error is never read. not implemented

//        $this->keysStorage[] = [
//            "userId"=>$userId,
//            "private"=>$encryptedPrivateKey,
//            "public"=>$publicKey
//        ];
    }

    public function getPasswordForUser(int $userId): ?string
    {
        return "test";
    }

    public function insertSession(string $hash, int $userId): void
    {
        // TODO: Implement insertSession() method.
    }

    public function checkIssetToken(string $token): ?array
    {
        return null;
    }

    public function getEmailList(int $userId): array
    {
        return [];
    }

    public function insertNewEmail(int $userId, string $encryptedEmail, string $emailHash, bool $allowAuth): int
    {
        return 0;
    }

    public function editEmailItem(int $userId, int $itemId, string $encryptedEmail, string $emailHash, bool $allowAuth): void
    {
        // TODO: Implement editEmailItem() method.
    }

    public function getEmailItem(int $userId, int $itemId): ?array
    {
        return null;
    }

    public function deleteEmail(int $userId, int $itemId): void
    {
        // TODO: Implement deleteEmail() method.
    }

    public function getPhonesList(int $userId): array
    {
        return [];
    }

    public function deletePhone(int $userId, int $itemId): void
    {
        // TODO: Implement deletePhone() method.
    }

    public function getPhoneItem(int $userId, int $itemId): ?array
    {
        return null;
    }
}