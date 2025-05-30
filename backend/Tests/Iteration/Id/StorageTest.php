<?php

namespace Flow\Tests\Iteration\Id;

use Flow\Core\Database;
use Flow\Core\Enums\ServicesEnum;
use Flow\Id\Models\EncryptedData;
use Flow\Id\Models\Password;
use Flow\Id\Storage\EmailStorage;
use Flow\Id\Storage\Migrations\Migration;
use Flow\Id\Storage\Migrations\Migration_0000;
use Flow\Id\Storage\Migrations\Migration_0001;
use Flow\Id\Storage\Migrations\Migration_0002;
use Flow\Id\Storage\Migrations\Migration_0003;
use Flow\Id\Storage\Migrations\Migration_0004;
use Flow\Id\Storage\PhoneStorage;
use Flow\Id\Storage\UserStorage;
use Flow\Tests\Feature\InitApp;
use Flow\Tests\Iteration\DbFunctions;
use mysqli;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Random;

#[CoversClass(UserStorage::class)]
#[CoversClass(Migration::class)]
#[CoversClass(Migration_0000::class)]
#[CoversClass(Migration_0001::class)]
#[CoversClass(Migration_0002::class)]
#[CoversClass(Migration_0003::class)]
#[CoversClass(Migration_0004::class)]
class StorageTest extends TestCase
{
    private mysqli $conn;
    private UserStorage $storage;

    protected function setUp(): void
    {
        InitApp::initTestEnv();
        $this->conn = Database::createConnection(ServicesEnum::Id);
        $this->storage = new UserStorage($this->conn);
    }

    public function testInsertUser(): void
    {
        $userId = $this->storage->insertUser(
            Uuid::v4(),
            new Password(Random::hash('test')),
            'testIV',
            'testSalt',
            new EncryptedData('fName'),
            new EncryptedData('lName'),
            new EncryptedData('bDay'),
            Random::hash(Random::get()),
        );
        $emailHash = Random::hash('test@test.com');
        $phoneStorage = new PhoneStorage();
        $emailStorage = new EmailStorage($this->conn);
        $emailStorage->insertNew($userId, 'email', $emailHash, true);
        $emailInfo = $this->storage->getUserByEmail($emailHash);
        $phoneHash = Random::hash('375333333333');
        $phoneStorage->insertNewPhone($userId, 'phone', $phoneHash, true);
        $phoneInfo = $this->storage->getUserByPhone($phoneHash);
        $this->assertNotNull($emailInfo);
        $this->assertNotNull($phoneInfo);
        $this->assertEquals($userId, $emailInfo['userId']);
    }

    protected function tearDown(): void
    {
        DbFunctions::dropTables($this->conn);
    }
}
