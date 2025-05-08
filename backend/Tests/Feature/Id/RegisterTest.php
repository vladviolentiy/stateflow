<?php

namespace Flow\Tests\Feature\Id;

use Flow\Tests\Feature\InitApp;
use Flow\Tests\Helpers\RegisterClientDtoSeeder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class RegisterTest extends TestCase
{
    private InitApp $app;

    public function setUp(): void
    {
        $this->app = new InitApp();
    }

    public function testRegister(): void
    {
        $client = RegisterClientDtoSeeder::create();

        $result = $this->app->sendQuery('POST', '/api/id/register', [
            'password' => $client->password->value,
            'iv' => $client->iv,
            'salt' => $client->salt,
            'publicKey' => $client->publicKey->value,
            'encryptedPrivateKey' => $client->encryptedPrivateKey->value,
            'fNameEncrypted' => $client->fNameEncrypted->value,
            'lNameEncrypted' => $client->lNameEncrypted->value,
            'bDayEncrypted' => $client->bDayEncrypted->value,
            'hash' => $client->hash,
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
