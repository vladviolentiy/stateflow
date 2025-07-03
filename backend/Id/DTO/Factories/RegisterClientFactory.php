<?php

namespace Flow\Id\DTO\Factories;

use Flow\Core\Interfaces\CreateFromRequestInterface;
use Flow\Id\DTO\RegisterClientDTO;
use Flow\Id\ValueObject\EncryptedData;
use Flow\Id\ValueObject\Password;
use Flow\Id\ValueObject\PrivateKey;
use Flow\Id\ValueObject\RsaPublicKey;
use Symfony\Component\HttpFoundation\Request;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class RegisterClientFactory implements CreateFromRequestInterface
{
    /**
     * @param string $iv
     * @param string $salt
     * @param string $hash
     * @phpstan-assert non-empty-string $iv
     * @phpstan-assert non-empty-string $salt
     * @phpstan-assert non-empty-string $hash
     * @return void
     * @throws ValidationException
     */
    private static function validate(
        string $iv,
        string $salt,
        string $hash,
    ): void {
        Validation::nonEmpty($iv);
        Validation::nonEmpty($salt);
        Validation::hash($hash);

        $decodedIv = base64_decode($iv);
        $decodedSalt = base64_decode($salt);

        if (
            $decodedSalt === $decodedIv ||
            strlen($decodedIv) !== 16 ||
            strlen($decodedSalt) !== 16
        ) {
            throw new ValidationException();
        }
    }

    public static function createFromRequest(Request $request): RegisterClientDTO
    {
        $iv = (string) $request->getPayload()->get('iv');
        $salt = (string) $request->getPayload()->get('salt');
        $hash = (string) $request->getPayload()->get('hash');

        self::validate($iv, $salt, $hash);

        $hash = hash('sha384', $hash . getenv('APP_TOKEN'));

        return new RegisterClientDTO(
            new Password((string) $request->getPayload()->get('password')),
            $iv,
            $salt,
            $hash,
            new RsaPublicKey((string) $request->getPayload()->get('publicKey')),
            new PrivateKey((string) $request->getPayload()->get('encryptedPrivateKey')),
            new EncryptedData((string) $request->getPayload()->get('fNameEncrypted')),
            new EncryptedData((string) $request->getPayload()->get('lNameEncrypted')),
            new EncryptedData((string) $request->getPayload()->get('bDayEncrypted')),
        );
    }
}
