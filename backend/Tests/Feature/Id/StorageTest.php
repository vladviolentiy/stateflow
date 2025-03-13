<?php

namespace Flow\Tests\Feature\Id;

use Flow\Id\Storage\Storage;
use Flow\Tests\Feature\DbFunctions;
use mysqli;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Random;

/**
 * @covers \Flow\Id\Storage\Storage
 * @covers \Flow\Id\Storage\Migrations\Migration
 * @covers \Flow\Id\Storage\Migrations\Migration_0000
 * @covers \Flow\Id\Storage\Migrations\Migration_0001
 * @covers \Flow\Id\Storage\Migrations\Migration_0002
 * @covers \Flow\Id\Storage\Migrations\Migration_0003
 */
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
            'test',
            'testIV',
            'testSalt',
            'fName',
            'lName',
            'bDay',
            Random::hash(Random::get()),
        );
        /** @phpstan-ignore-next-line  */
        $this->assertIsInt($userId);
        $emailHash = Random::hash('test@test.com');
        $this->storage->insertNewEmail($userId, 'email', $emailHash, true);
        $emailInfo = $this->storage->getUserByEmail($emailHash);
        $this->assertNotNull($emailInfo);
        $this->assertEquals($userId, $emailInfo['userId']);

    }

    protected function tearDown(): void
    {
        DbFunctions::dropTables($this->conn);
    }
}
