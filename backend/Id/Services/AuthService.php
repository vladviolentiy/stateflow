<?php

namespace Flow\Id\Services;

use Flow\Core\Exceptions\AuthenticationException;
use Flow\Core\Exceptions\DatabaseException;
use Flow\Core\Exceptions\IncorrectPasswordException;
use Flow\Id\DTO\RegisterClientDTO;
use Flow\Id\Models\Password;
use Flow\Id\Resources\AuthResource;
use Flow\Id\Resources\RegisterResource;
use Flow\Id\Storage\Interfaces\SessionStorageInterface;
use Flow\Id\Storage\Interfaces\UserStorageInterface;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Validation;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Random;
use Flow\Id\Enums\AuthMethods;
use Flow\Id\Enums\AuthVia;

class AuthService extends BaseController
{
    /**
     * @param UserStorageInterface $storage
     * @param SessionStorageInterface $sessionStorage
     */
    public function __construct(
        private readonly UserStorageInterface $storage,
        private readonly SessionStorageInterface $sessionStorage,
    ) {
        parent::__construct();
    }

    public function createNewUser(RegisterClientDTO $request): RegisterResource
    {
        $uuid = UUID::v4();

        $userId = $this->storage->insertUser(
            $uuid,
            $request->password,
            $request->iv,
            $request->salt,
            $request->fNameEncrypted,
            $request->lNameEncrypted,
            $request->bDayEncrypted,
            $request->hash,
        );
        $this->storage->insertNewEncryptInfo($userId, $request->publicKey, $request->encryptedPrivateKey);

        return RegisterResource::fromState([
            'uuid' => $uuid
        ]);
    }

    /**
     * @param string $userInfo
     * @param AuthMethods $authTypesEnum
     * @return array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}
     * @throws ValidationException
     * @throws DatabaseException
     */
    private function getUserInfoAuth(string $userInfo, AuthMethods $authTypesEnum): array
    {
        if ($authTypesEnum === AuthMethods::UUID) {
            Validation::uuid($userInfo);
            $userInfo = $this->storage->getUserByUUID(Uuid::fromString($userInfo));
        } elseif ($authTypesEnum === AuthMethods::Email) {
            Validation::hash($userInfo);
            $userInfo = $this->storage->getUserByEmail($userInfo);
        } elseif ($authTypesEnum === AuthMethods::Phone) {
            Validation::hash($userInfo);
            $userInfo = $this->storage->getUserByPhone($userInfo);
        }

        if ($userInfo === null) {
            throw new \VladViolentiy\VivaFramework\Exceptions\NotfoundException();
        }

        return $userInfo;
    }

    /**
     * @param string $userInfo
     * @param AuthMethods $authTypesEnum
     * @return array{salt:string, iv:string}
     */
    public function getAuthDataForUser(string $userInfo, AuthMethods $authTypesEnum): array
    {
        $userInfo = $this->getUserInfoAuth($userInfo, $authTypesEnum);
        unset($userInfo['userId']);
        unset($userInfo['password']);

        return $userInfo;
    }

    /**
     * @param string $token
     * @return array{userId:positive-int,lang:non-empty-string}
     * @throws AuthenticationException
     */
    public function checkAuth(string $token): array
    {
        Validation::hash($token);
        $userInfo = $this->sessionStorage->checkIssetToken($token);
        unset($userInfo['sessionId']);
        if ($userInfo === null) {
            throw new AuthenticationException();
        }
        $userInfo['ip'] = $_SERVER['REMOTE_ADDR'];
        $userInfo['ua'] = $_SERVER['HTTP_USER_AGENT'];
        $userInfo['acceptEncoding'] = $_SERVER['HTTP_ACCEPT_ENCODING'];
        $userInfo['acceptLang'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        return $userInfo;
    }

    /**
     * @param string $userInfo
     * @param AuthMethods $authTypesEnum
     * @param string $authString
     * @return AuthResource
     * @throws DatabaseException
     * @throws IncorrectPasswordException
     * @throws ValidationException
     */
    public function auth(string $userInfo, AuthMethods $authTypesEnum, AuthVia $authVia, string $authString): AuthResource
    {
        Validation::nonEmpty($authString);

        if ($authVia !== AuthVia::Password) {
            throw new AuthenticationException();
        }

        $userInfo = $this->getUserInfoAuth($userInfo, $authTypesEnum);
        if (!password_verify($authString, $userInfo['password'])) {
            throw new IncorrectPasswordException();
        }
        $hash = Random::hash(Random::get());
        $this->sessionStorage->insertSession($hash, $userInfo['userId']);

        return AuthResource::fromState([
            'hash' => $hash,
            'salt' => $userInfo['salt'],
            'iv' => $userInfo['iv'],
        ]);
    }
}
