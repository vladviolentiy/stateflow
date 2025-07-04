<?php

namespace Flow\Tests\Unit\Id;

use Flow\Core\Exceptions\AuthenticationException;
use Flow\Core\Exceptions\IncorrectPasswordException;
use Flow\Core\Validation;
use Flow\Core\Validations\EncryptedDataValidator;
use Flow\Core\Validations\RsaPublicKeyValidator;
use Flow\Id\DTO\RegisterClientDTO;
use Flow\Id\ValueObject\EncryptedData;
use Flow\Id\ValueObject\Password;
use Flow\Id\ValueObject\PrivateKey;
use Flow\Id\ValueObject\RsaPublicKey;
use Flow\Id\Resources\RegisterResource;
use Flow\Id\Services\AuthService;
use Flow\Id\Services\BaseService;
use Flow\Id\Enums\AuthMethods;
use Flow\Id\Enums\AuthVia;
use Flow\Id\Storage\ArrayStorage\EmailArrayStorage;
use Flow\Id\Storage\ArrayStorage\PhoneArrayStorage;
use Flow\Id\Storage\ArrayStorage\SessionArrayStorage;
use Flow\Id\Storage\ArrayStorage\UserArrayStorage;
use Flow\Tests\Helpers\RegisterClientDtoSeeder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Exceptions\NotfoundException;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;

#[CoversClass(AuthService::class)]
#[CoversClass(BaseService::class)]
#[CoversClass(UserArrayStorage::class)]
#[CoversClass(Validation::class)]
#[CoversClass(EncryptedDataValidator::class)]
#[CoversClass(RsaPublicKeyValidator::class)]
#[CoversClass(AuthenticationException::class)]
#[CoversClass(IncorrectPasswordException::class)]
class AuthTest extends TestCase
{
    private AuthService $auth;
    /**
     * @var Uuid[]
     */
    private array $uuidList = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->auth = new AuthService(new UserArrayStorage(new EmailArrayStorage(), new PhoneArrayStorage()), new SessionArrayStorage());
    }

    public function testCreatingNewUser(): void
    {
        $data = $this->createNewUser();
        $this->assertNotEmpty($data->uuid);
    }

    public function testIncorrectInfo(): void
    {
        $this->expectException(ValidationException::class);

        $password = hash('sha384', 'testPassword');
        $hash = hash('sha384', 'TESTDATA');
        $iv = base64_encode(random_bytes(12));
        $salt = base64_encode(random_bytes(4));

        $dto = new RegisterClientDTO(
            new Password($password),
            $iv,
            $salt,
            $hash,
            new RsaPublicKey(''),
            new PrivateKey(''),
            new EncryptedData(''),
            new EncryptedData(''),
            new EncryptedData(''),
        );

        $this->auth->createNewUser($dto);
    }

    public function testGetUserInfo(): void
    {
        $user = $this->createNewUser();
        $info = $this->auth->getAuthDataForUser($user->uuid, AuthMethods::UUID);
        $this->assertArrayHasKey('iv', $info);
    }

    private function createNewUser(): RegisterResource
    {
        $user = RegisterClientDtoSeeder::create();

        $uuid = $this->auth->createNewUser($user);

        $this->uuidList[] = $uuid->uuid;

        return $uuid;
    }

    public function testCrashOnNonPasswordExcept(): void
    {
        $this->createNewUser();
        $uuid = $this->uuidList[0];
        $this->expectException(AuthenticationException::class);
        $this->auth->auth(
            $uuid,
            AuthMethods::from('uuid'),
            AuthVia::Fingerprint,
            hash('sha384', 'testPassword'),
        );
    }

    public function testBaseAuth(): void
    {
        $this->createNewUser();
        $uuid = $this->uuidList[0];

        $result = $this->auth->auth(
            $uuid,
            AuthMethods::from('uuid'),
            AuthVia::Password,
            hash('sha384', 'testPassword'),
        );
        $this->assertNotContains('password', $result->toArray());
    }

    public function testBadPassword(): void
    {
        $this->createNewUser();
        $uuid = $this->uuidList[0];

        $this->expectException(NotfoundException::class);
        $this->auth->auth(
            $uuid,
            AuthMethods::from('uuid'),
            AuthVia::Password,
            hash('sha384', 'pass'),
        );
    }
}
