<?php

namespace Flow\Tests\Feature\Id;

use Flow\Id\Models\Password;
use Flow\Id\Storage\Migrations\Migration;
use Flow\Id\Storage\Migrations\Migration_0000;
use Flow\Id\Storage\Migrations\Migration_0001;
use Flow\Id\Storage\Migrations\Migration_0002;
use Flow\Id\Storage\Migrations\Migration_0003;
use Flow\Id\Storage\Migrations\Migration_0004;
use Flow\Id\Storage\Storage;
use Flow\Tests\Feature\DbFunctions;
use mysqli;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Random;

#[CoversClass(Storage::class)]
#[CoversClass(Migration::class)]
#[CoversClass(Migration_0000::class)]
#[CoversClass(Migration_0001::class)]
#[CoversClass(Migration_0002::class)]
#[CoversClass(Migration_0003::class)]
#[CoversClass(Migration_0004::class)]
class StorageTest extends TestCase
{
    private mysqli $conn;
    private Storage $storage;

    protected function setUp(): void
    {

        $dotenv = new Dotenv();
        $dotenv->usePutenv()->loadEnv(__DIR__ . '/../../../../.env');

        $this->conn = new mysqli(
            (string) getenv('DB_TEST_SERVER'),
            (string) getenv('DB_TEST_USER'),
            (string) getenv('DB_TEST_PASSWORD'),
            (string) getenv('DB_TEST_DATABASE'),
        );

        $this->storage = new Storage($this->conn);
    }

    public function testInsertUser(): void
    {
        $userId = $this->storage->insertUser(
            Uuid::v4(),
            new Password('test'),
            'testIV',
            'testSalt',
            'fName',
            'lName',
            'bDay',
            Random::hash(Random::get()),
        );
        $emailHash = Random::hash('test@test.com');
        $this->storage->insertNewEmail($userId, 'email', $emailHash, true);
        $emailInfo = $this->storage->getUserByEmail($emailHash);
        $phoneHash = Random::hash('375333333333');
        $this->storage->insertNewPhone($userId, 'phone', $phoneHash, true);
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
