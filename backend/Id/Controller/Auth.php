<?php

namespace Flow\Id\Controller;

use Flow\Core\Exceptions\AuthenticationException;
use Flow\Core\Exceptions\DatabaseException;
use Flow\Core\Exceptions\IncorrectPasswordException;
use Flow\Core\Exceptions\NotfoundException;
use VladViolentiy\VivaFramework\Validation;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Random;
use Flow\Id\Enums\AuthMethods;
use Flow\Id\Enums\AuthVia;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Auth extends Base
{
    public function createNewUser(
        string $password,
        string $iv,
        string $salt,
        string $publicKey,
        string $encryptedPrivateKey,
        string $fNameEncrypted,
        string $lNameEncrypted,
        string $bDayEncrypted,
        string $hash,
    ):UuidInterface{

        Validation::nonEmpty($password);
        Validation::nonEmpty($iv);
        Validation::nonEmpty($salt);
        Validation::nonEmpty($fNameEncrypted);
        Validation::nonEmpty($lNameEncrypted);
        Validation::nonEmpty($bDayEncrypted);
        Validation::nonEmpty($hash);

        $decodedIv = base64_decode($iv);
        $decodedSalt = base64_decode($salt);
        Validation::hash($password);
        Validation::hash($hash);
        if(
            $decodedSalt===$decodedIv or
            strlen($decodedIv)!==16 or
            strlen($decodedSalt)!==16
        ) throw new ValidationException();

        $uuid = UUID::uuid4();
        /** @var non-empty-string $passwordHash */
        $passwordHash = password_hash($password,PASSWORD_BCRYPT);
        $userId = $this->storage->addNewUser($uuid,$passwordHash,$iv,$salt,$fNameEncrypted,$lNameEncrypted,$bDayEncrypted,$hash);
        $this->storage->insertNewEncryptInfo($userId,$publicKey,$encryptedPrivateKey);
        return $uuid;
    }

    /**
     * @param string $userInfo
     * @param AuthMethods $authTypesEnum
     * @return array{userId:int,salt:string,iv:string}
     * @throws NotfoundException
     * @throws ValidationException
     * @throws DatabaseException
     */
    private function getUserInfoAuth(string $userInfo, AuthMethods $authTypesEnum):array{
        if($userInfo==="") throw new ValidationException();
        if($authTypesEnum === AuthMethods::UUID){
            Validation::uuid($userInfo);
            $userInfo = $this->storage->getUserByUUID(Uuid::fromString($userInfo));
        } elseif ($authTypesEnum === AuthMethods::Email) {
            Validation::hash($userInfo);
            $userInfo = $this->storage->getUserByEmail($userInfo);
        } elseif ($authTypesEnum === AuthMethods::Phone) {
            Validation::hash($userInfo);
            $userInfo = $this->storage->getUserByPhone($userInfo);
        } else {
            throw new ValidationException();
        }
        if($userInfo===null) throw new NotfoundException();
        return $userInfo;
    }

    /**
     * @param string $userInfo
     * @param AuthMethods $authTypesEnum
     * @return array{salt:string,iv:string}
     * @throws NotfoundException
     */
    public function getAuthDataForUser(string $userInfo, AuthMethods $authTypesEnum):array{
        $userInfo = $this->getUserInfoAuth($userInfo,$authTypesEnum);
        unset($userInfo['userId']);
        return $userInfo;
    }

    /**
     * @param string $token
     * @return array{userId:positive-int,lang:string}
     * @throws AuthenticationException
     */
    public function checkAuth(string $token):array{
        Validation::hash($token);
        $userInfo = $this->storage->checkIssetToken($token);
        if($userInfo===null) throw new AuthenticationException();
        return $userInfo;
    }

    /**
     * @param string $userInfo
     * @param AuthMethods $authTypesEnum
     * @param string $authString
     * @return array{hash:string}
     * @throws DatabaseException
     * @throws IncorrectPasswordException
     * @throws NotfoundException
     * @throws ValidationException
     */
    public function auth(string $userInfo, AuthMethods $authTypesEnum,AuthVia $authVia, string $authString):array{
        $userInfo = $this->getUserInfoAuth($userInfo,$authTypesEnum);
        if($authString==="") throw new ValidationException();
        if($authVia==AuthVia::Password){
            $passwordHash = $this->storage->getPasswordForUser($userInfo['userId']);
            if($passwordHash===null) throw new ValidationException();
            if(!password_verify($authString,$passwordHash)) throw new IncorrectPasswordException();
        }
        $hash = Random::hash(Random::get());
        $this->storage->insertSession($hash,$userInfo['userId']);
        return [
            "hash"=>$hash,
            "salt"=>$userInfo['salt'],
            "iv"=>$userInfo['iv']
        ];
    }
}