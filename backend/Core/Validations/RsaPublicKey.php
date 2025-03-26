<?php

namespace Flow\Core\Validations;

use VladViolentiy\VivaFramework\Exceptions\ValidationException;

final class RsaPublicKey implements ValidationInterface
{
    public function validate(string $input): true
    {
        if (!str_starts_with($input, '-----BEGIN PUBLIC KEY-----')) {
            $input = "
-----BEGIN PUBLIC KEY-----
$input
-----END PUBLIC KEY-----
";
        }
        $publicKey = openssl_get_publickey($input);
        if (!$publicKey) {
            throw new ValidationException('Invalid public key');
        }

        // Extract the key details
        $details = openssl_pkey_get_details($publicKey);
        if (!$details) {
            throw new ValidationException('Invalid public key');
        }
        if ($details['type'] !== OPENSSL_KEYTYPE_RSA) {
            throw new ValidationException('Provided data is not a RSA');
        }

        return true;
    }
}
