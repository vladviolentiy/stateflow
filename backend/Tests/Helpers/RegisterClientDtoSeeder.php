<?php

namespace Flow\Tests\Helpers;

use DateTimeImmutable;
use Exception;
use Flow\Id\DTO\RegisterClientDTO;
use Flow\Id\ValueObject\EncryptedData;
use Flow\Id\ValueObject\Password;
use Flow\Id\ValueObject\PrivateKey;
use Flow\Id\ValueObject\RsaPublicKey;
use Flow\Tests\Unit\Methods\RSAHelpers;
use OpenSSLAsymmetricKey;
use Random\RandomException;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Random;

class RegisterClientDtoSeeder
{
    public const string PASSWORD = 'TestPassword!';

    /**
     * @param string $salt
     * @return non-empty-string
     */
    private static function getPbkdfedPassword(string $salt): string
    {
        return hash_pbkdf2('sha256', self::PASSWORD, $salt, 100000);
    }

    /**
     * @param non-empty-string $iv
     * @return RegisterClientDTO
     * @throws ValidationException
     * @throws RandomException
     */
    public static function renderBy(string $iv): RegisterClientDTO
    {
        $rsaKey = RSAHelpers::createKeyPair(4096);

        $salt = EncryptedDataSeeder::randomData();
        $password = self::getPbkdfedPassword($salt);

        $keyDetail = openssl_pkey_get_details($rsaKey);
        if ($keyDetail === false) {
            throw new Exception('Failed to get private key');
        }
        /** @var string $publicKey */
        $publicKey = $keyDetail['key'];
        $encyptedPrivateKey = self::encryptPrivateKey($rsaKey, $password, $iv);
        $fName = openssl_encrypt('testUserfName', 'AES-256-CBC', $password, iv: $iv);
        $lName = openssl_encrypt('testUserlName', 'AES-256-CBC', $password, iv: $iv);
        $bDay = openssl_encrypt((new DateTimeImmutable('2000-01-01'))->format('Y-m-d'), 'AES-256-CBC', $password, iv: $iv);
        if ($fName === false || $lName === false || $bDay === false) {
            throw new Exception('Failed to create private key');
        }
        $hash = Random::hash(sprintf('test-test-2000-01-01-%s', $salt));
        $password = Random::hash($password);

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

    public static function create(): RegisterClientDTO
    {
        while (true) {
            try {
                $iv = EncryptedDataSeeder::randomData();

                return self::renderBy($iv);
            } catch (ValidationException $exception) {

            }
        }
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
