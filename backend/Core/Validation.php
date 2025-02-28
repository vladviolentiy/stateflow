<?php

namespace Flow\Core;

use VladViolentiy\VivaFramework\Exceptions\ValidationException;

class Validation
{
    /**
     * @param string $keyInput
     * @return bool
     * @phpstan-assert non-empty-string $keyInput
     * @throws ValidationException
     */
    public static function RSAPublicKey(string $keyInput): bool
    {
        if (!str_starts_with($keyInput, '-----BEGIN PUBLIC KEY-----')) {
            $keyInput = "
-----BEGIN PUBLIC KEY-----
$keyInput
-----END PUBLIC KEY-----
";
        }
        $publicKey = openssl_get_publickey($keyInput);
        if (!$publicKey) {
            throw new ValidationException();
        }

        // Extract the key details
        $details = openssl_pkey_get_details($publicKey);
        if (!$details) {
            throw new ValidationException();
        }
        if ($details['type'] !== OPENSSL_KEYTYPE_RSA) {
            throw new ValidationException();
        }

        return true;
    }

    public static function encryptedData(string $data): bool
    {
        \VladViolentiy\VivaFramework\Validation::nonEmpty($data);

        $decodedData = base64_decode($data, true);
        if ($decodedData === false) {
            throw new ValidationException('Invalid base64 data');
        }

        $strlen = strlen($decodedData);
        if ($strlen % 16 !== 0) {
            throw new ValidationException('Invalid encrypted data');
        }
        /** @var array<string,positive-int> $freq */
        $freq = array_count_values(str_split($decodedData));
        $self = new self();
        $entropy = $self->calculateEntropy($freq, $strlen);
        $asciiStat = $self->check_ascii($decodedData);

        $log = log(count($freq), 2);
        if ($log == 0 || $entropy === 0.0 || $entropy < $log * 0.9) {
            throw new ValidationException('Bad encrypted data');
        }
        if ($asciiStat > 0.38) {
            throw new ValidationException('Bad encrypted data (ascii');
        }

        return true;
    }

    private function check_ascii(string $decoded_string): float
    {
        $ascii_count = 0;
        for ($i = 0; $i < strlen($decoded_string); $i++) {
            $char = ord($decoded_string[$i]);
            if ($char >= 32 && $char <= 126) { // ASCII-символы
                $ascii_count++;
            }
        }
        $ascii_ratio = $ascii_count / strlen($decoded_string);

        return $ascii_ratio;
    }

    /**
     * @param array<string,int> $frequencies
     * @param int $length
     * @return float
     */
    private function calculateEntropy(array $frequencies, int $length): float
    {

        $entropy = 0.0;
        foreach ($frequencies as $count) {
            $probability = $count / $length;
            $entropy -= $probability * log($probability, 2); // Формула энтропии
        }

        return $entropy;
    }
}
