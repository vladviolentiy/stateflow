<?php

namespace Flow\Tests\Feature\Id;

use Flow\Tests\Feature\EncryptedDataSeeder;
use Flow\Tests\Feature\InitApp;
use Flow\Tests\Unit\Methods\RSAHelpers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use VladViolentiy\VivaFramework\Random;

class RegisterTest extends TestCase
{
    private const string PASSWORD = 'MyTestPassword!';
    private InitApp $app;

    public function setUp(): void
    {
        $this->app = new InitApp();
    }

    public function testRegister(): void
    {
        $rsaKey = RSAHelpers::createKeyPair(4096);
        $privateKeyDer = openssl_pkey_get_details($rsaKey);
        if ($privateKeyDer === false) {
            throw new \Exception('Failed to get private key');
        }
        $publicKey = $privateKeyDer['key'];
        openssl_pkey_export($rsaKey, $privateKey);
        /** @var string $privateKeyString */
        $privateKeyString = $privateKey;
        $cleaner = str_replace(["\n", '-----BEGIN PRIVATE KEY-----', '-----END PRIVATE KEY-----'], '', $privateKeyString);
        $decoded = base64_decode($cleaner);

        $iv = EncryptedDataSeeder::randomData();
        $encyptedPrivateKey = openssl_encrypt($decoded, 'AES-256-CBC', self::PASSWORD, iv: $iv);
        $fName = openssl_encrypt('test', 'AES-256-CBC', self::PASSWORD, iv: $iv);
        $lName = openssl_encrypt('test', 'AES-256-CBC', self::PASSWORD, iv: $iv);
        $bDay = openssl_encrypt((new \DateTimeImmutable('2000-01-01'))->format('Y-m-d'), 'AES-256-CBC', self::PASSWORD, iv: $iv);
        $salt = EncryptedDataSeeder::randomData();
        $hash = Random::hash(sprintf('test-test-2000-01-01-%s', $salt));
        $password = Random::hash(self::PASSWORD);

        $result = $this->app->sendQuery('POST', '/api/id/register', [
            'password' => $password,
            'iv' => EncryptedDataSeeder::randomData(),
            'salt' => EncryptedDataSeeder::randomData(),
            'publicKey' => $publicKey,
            'encryptedPrivateKey' => $encyptedPrivateKey,
            'fNameEncrypted' => $fName,
            'lNameEncrypted' => $lName,
            'bDayEncrypted' => $bDay,
            'hash' => $hash,
        ]);

        $this->assertEquals(200, $result->getStatusCode());
        $content = $result->getContent();
        $this->assertNotFalse($content);
        /** @var object{success:false, text: string}|object{success:true, data: object{uuid: non-empty-string}}|null $decode */
        $decode = json_decode($content, flags: JSON_THROW_ON_ERROR);
        $this->assertNotNull($decode);
        $this->assertTrue($decode->success);
        if (isset($decode->data)) {
            $this->assertInstanceOf(Uuid::class, Uuid::fromString($decode->data->uuid));
        }
    }
}
