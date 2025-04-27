<?php

namespace Flow\Core\Validations;

use VladViolentiy\VivaFramework\Exceptions\ValidationException;

final class RsaPublicKey implements ValidationInterface
{
    public function validate(string $input, string $field): true
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
            throw new ValidationException(sprintf('Invalid public key in %s', $field));
        }

        // Extract the key details
        $details = openssl_pkey_get_details($publicKey);
        if (!$details) {
            throw new ValidationException(sprintf('Invalid public key in %s', $field));
        }
        if ($details['type'] !== OPENSSL_KEYTYPE_RSA) {
            throw new ValidationException(sprintf('Provided data is not a RSA %s', $field));
        }

        return true;
    }
}
