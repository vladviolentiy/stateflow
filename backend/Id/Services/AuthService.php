<?php

namespace Flow\Id\Services;

use Flow\Core\Exceptions\AuthenticationException;
use Flow\Core\Exceptions\IncorrectPasswordException;
use Flow\Id\DTO\RegisterClientDTO;
use Flow\Id\Resources\AuthResource;
use Flow\Id\Resources\RegisterResource;
use Flow\Id\Storage\Interfaces\SessionStorageInterface;
use Flow\Id\Storage\Interfaces\UserStorageInterface;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Exceptions\NotfoundException;
use VladViolentiy\VivaFramework\Validation;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Random;
use Flow\Id\Enums\AuthMethods;
use Flow\Id\Enums\AuthVia;

class AuthService extends BaseService
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
            'uuid' => $uuid,
        ]);
    }

    /**
     * @param string $userInfo
     * @param AuthMethods $authTypesEnum
     * @return array{userId:positive-int, salt:non-empty-string, iv:non-empty-string, password:non-empty-string}
     * @throws ValidationException
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
            return [
                'userId' => 1,
                'password' => 'empty',
                'salt' => base64_encode(substr(hash('sha384', getenv('APP_TOKEN') . 'salt' . $userInfo, true), 0, 16)),
                'iv' => base64_encode(substr(hash('sha384', getenv('APP_TOKEN') . 'iv' . $userInfo, true), 0, 16)),
            ];
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
     * @return array{userId:positive-int, lang:non-empty-string, sessionId:positive-int}
     * @throws AuthenticationException
     */
    public function checkAuth(string $token): array
    {
        Validation::hash($token);
        $userInfo = $this->sessionStorage->checkIssetToken($token);
        if ($userInfo === null) {
            throw new AuthenticationException();
        }

        return $userInfo;
    }

    /**
     * @param string $userInfo
     * @param AuthMethods $authTypesEnum
     * @param string $authString
     * @return AuthResource
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
            throw new NotfoundException();
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
