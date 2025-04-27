<?php

namespace Flow\Id\DTO;

use Flow\Id\Models\EncryptedData;
use Flow\Id\Models\Password;
use Flow\Id\Models\PrivateKey;
use Flow\Id\Models\RsaPublicKey;
use Symfony\Component\HttpFoundation\Request;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

class RegisterClientDTO
{
    /** @var non-empty-string  */
    public string $iv;
    /** @var non-empty-string  */
    public string $salt;
    /** @var non-empty-string  */
    public string $hash;

    /**
     * @param Password $password
     * @param string $iv
     * @param string $salt
     * @param string $hash
     * @param RsaPublicKey $publicKey
     * @param PrivateKey $encryptedPrivateKey
     * @param EncryptedData $fNameEncrypted
     * @param EncryptedData $lNameEncrypted
     * @param EncryptedData $bDayEncrypted
     * @throws ValidationException
     */
    public function __construct(
        public Password $password,
        string $iv,
        string $salt,
        string $hash,
        public RsaPublicKey $publicKey,
        public PrivateKey $encryptedPrivateKey,
        public EncryptedData $fNameEncrypted,
        public EncryptedData $lNameEncrypted,
        public EncryptedData $bDayEncrypted,
    ) {
        $this->validate($iv, $salt, $hash);
    }

    private function validate(
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
        $this->iv = $iv;
        $this->salt = $salt;
        $this->hash = $hash;
    }

    public static function createFrom(Request $request): self
    {
        return new self(
            new Password((string) $request->getPayload()->get('password')),
            (string) $request->getPayload()->get('iv'),
            (string) $request->getPayload()->get('salt'),
            (string) $request->getPayload()->get('hash'),
            new RsaPublicKey((string) $request->getPayload()->get('publicKey')),
            new PrivateKey((string) $request->getPayload()->get('encryptedPrivateKey')),
            new EncryptedData((string) $request->getPayload()->get('fNameEncrypted')),
            new EncryptedData((string) $request->getPayload()->get('lNameEncrypted')),
            new EncryptedData((string) $request->getPayload()->get('bDayEncrypted')),
        );
    }
}
