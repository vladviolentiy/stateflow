<?php

namespace Flow\Tests\Helpers;

use DateTimeImmutable;
use Exception;
use Flow\Id\DTO\RegisterClientDTO;
use Flow\Id\Models\EncryptedData;
use Flow\Id\Models\Password;
use Flow\Id\Models\PrivateKey;
use Flow\Id\Models\RsaPublicKey;
use Flow\Tests\Unit\Methods\RSAHelpers;
use OpenSSLAsymmetricKey;
use VladViolentiy\VivaFramework\Random;

class RegisterClientDtoSeeder
{
    public const string PASSWORD = 'TestPassword!';

    private static function getPbkdfedPassword(string $salt): string
    {
        return hash_pbkdf2('sha256', self::PASSWORD, $salt, 100000);
    }

    public static function create(): RegisterClientDTO
    {
        $rsaKey = RSAHelpers::createKeyPair(4096);

        $salt = EncryptedDataSeeder::randomData();
        $password = self::getPbkdfedPassword($salt);

        $keyDetail = openssl_pkey_get_details($rsaKey);
        if ($keyDetail === false) {
            throw new Exception('Failed to get private key');
        }
        $publicKey = $keyDetail['key'];
        $iv = EncryptedDataSeeder::randomData();
        $encyptedPrivateKey = self::encryptPrivateKey($rsaKey, $password, $iv);
        $fName = openssl_encrypt('Violentiy', 'AES-256-CBC', $password, iv: $iv);
        $lName = openssl_encrypt('Vladislav', 'AES-256-CBC', $password, iv: $iv);
        $bDay = openssl_encrypt((new DateTimeImmutable('2000-01-01'))->format('Y-m-d'), 'AES-256-CBC', $password, iv: $iv);
        $hash = Random::hash(sprintf('test-test-2000-01-01-%s', $salt));
        $password = Random::hash($password);

        dump($fName);

        return new RegisterClientDTO(
            new Password($password),
            base64_encode($iv),
            $salt,
            $hash,
            new RsaPublicKey($publicKey),
            new PrivateKey($encyptedPrivateKey),
            new EncryptedData($fName),
            new EncryptedData($lName),
            new EncryptedData($bDay),
        );
    }

    private static function encryptPrivateKey(OpenSSLAsymmetricKey $rsaKey, string $password, string $iv): string
    {
        openssl_pkey_export($rsaKey, $privateKey);
        /** @var string $privateKeyString */
        $privateKeyString = $privateKey;
        $cleaner = str_replace(["\n", '-----BEGIN PRIVATE KEY-----', '-----END PRIVATE KEY-----'], '', $privateKeyString);
        $decoded = base64_decode($cleaner);
        $encyptedPrivateKey = openssl_encrypt($decoded, 'AES-256-CBC', $password, iv: $iv);
        if ($encyptedPrivateKey === false) {
            throw new Exception('Failed to encrypt private key');
        }

        return $encyptedPrivateKey;
    }
}
