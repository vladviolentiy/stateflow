<?php

namespace Flow\Tests\Unit\Id;

use Flow\Id\Controller\AuthController;
use Flow\Id\Enums\AuthMethods;
use Flow\Id\Storage\UsersArrayStorage;
use Flow\Tests\Unit\Methods\RSA;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;

/**
 * @covers \Flow\Id\Controller\AuthController
 * @covers \Flow\Id\Controller\BaseController
 * @covers \Flow\Id\Storage\UsersArrayStorage
 * @covers \Flow\Core\Validation
 */
class AuthTest extends TestCase
{
    private AuthController $auth;
    /**
     * @var Uuid[]
     */
    private array $uuidList = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->auth = new AuthController(new UsersArrayStorage());
    }

    public function testCreatingNewUser(): void
    {
        $data = $this->createNewUser();
        $this->assertTrue($data);
    }

    public function testIncorrectInfo(): void
    {
        $this->expectException(ValidationException::class);

        $password = hash('sha384', 'testPassword');
        $hash = hash('sha384', 'TESTDATA');
        $iv = base64_encode(random_bytes(12));
        $salt = base64_encode(random_bytes(4));
        $this->auth->createNewUser(
            $password,
            $iv,
            $salt,
            'RSAPUBLIC',
            'RSAPRIVATE',
            'TEST',
            'TEST',
            'TEST',
            $hash,
        );
    }

    public function testGetUserInfo(): void
    {
        $this->createNewUser();
        foreach ($this->uuidList as $item) {
            $info = $this->auth->getAuthDataForUser($item, AuthMethods::UUID);
            $this->assertEquals(base64_encode('1234567890abcdef'), $info['iv']);
        }
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    public function createNewUser(): bool
    {
        $password = hash('sha384', 'testPassword');
        $hash = hash('sha384', 'TESTDATA');
        $iv = base64_encode('1234567890abcdef');
        $salt = base64_encode(random_bytes(16));

        $public = RSA::createPublicKey(2048);

        $uuid = $this->auth->createNewUser(
            $password,
            $iv,
            $salt,
            $public,
            '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',
            'UQEYCAcLxBT+3SaJ6pD5iA==',
            'f5+2/k/J7xVSlKsBBxFNEQ==',
            'DT4kYWoTzNS/9Xml9MG3cQ==',
            $hash,
        );
        $this->uuidList[] = $uuid;

        return true;
    }
}
