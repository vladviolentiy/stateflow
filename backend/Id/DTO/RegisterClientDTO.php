<?php

namespace Flow\Id\DTO;

use Flow\Core\Interfaces\DtoInterface;
use Flow\Id\Models\EncryptedData;
use Flow\Id\Models\Password;
use Flow\Id\Models\PrivateKey;
use Flow\Id\Models\RsaPublicKey;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RegisterClientDTO',
    required: ['password', 'iv', 'salt', 'fNameEncrypted', 'lNameEncrypted', 'bDayEncrypted', 'hash', 'publicKey', 'encryptedPrivateKey'],
    properties: [
        new OA\Property(property: 'password', description: 'User password', type: 'string'),
        new OA\Property(property: 'iv', description: 'Initialization vector for encryption', type: 'string'),
        new OA\Property(property: 'salt', description: 'Salt for password hashing', type: 'string'),
        new OA\Property(property: 'fNameEncrypted', description: 'Encrypted first name', type: 'string'),
        new OA\Property(property: 'lNameEncrypted', description: 'Encrypted last name', type: 'string'),
        new OA\Property(property: 'bDayEncrypted', description: 'Encrypted birthdate', type: 'string'),
        new OA\Property(property: 'hash', description: 'Hash of user data', type: 'string'),
        new OA\Property(property: 'publicKey', description: "User's public key", type: 'string'),
        new OA\Property(property: 'encryptedPrivateKey', description: 'Encrypted private key', type: 'string'),
    ],
    type: 'object',
)]
readonly class RegisterClientDTO implements DtoInterface
{
    /**
     * @param Password $password
     * @param non-empty-string $iv
     * @param non-empty-string $salt
     * @param non-empty-string $hash
     * @param RsaPublicKey $publicKey
     * @param PrivateKey $encryptedPrivateKey
     * @param EncryptedData $fNameEncrypted
     * @param EncryptedData $lNameEncrypted
     * @param EncryptedData $bDayEncrypted
     */
    public function __construct(
        public Password $password,
        public string $iv,
        public string $salt,
        public string $hash,
        public RsaPublicKey $publicKey,
        public PrivateKey $encryptedPrivateKey,
        public EncryptedData $fNameEncrypted,
        public EncryptedData $lNameEncrypted,
        public EncryptedData $bDayEncrypted,
    ) {}
}
