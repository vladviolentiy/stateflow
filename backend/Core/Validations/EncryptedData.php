<?php

namespace Flow\Core\Validations;

use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

final class EncryptedData implements ValidationInterface
{
    public function validate(string $input): true
    {
        Validation::nonEmpty($input);

        $decodedData = base64_decode($input, true);
        if ($decodedData === false) {
            throw new ValidationException('Invalid base64 data');
        }

        $strlen = strlen($decodedData);
        if ($strlen % 16 !== 0 || $strlen === 0) {
            throw new ValidationException('Invalid encrypted data');
        }
        /** @var array<string,positive-int> $freq */
        $freq = array_count_values(str_split($decodedData));
        $entropy = $this->calculateEntropy($freq, $strlen);

        $log = log(count($freq), 2);
        if ($log == 0 || $entropy === 0.0 || $entropy < $log * 0.9) {
            throw new ValidationException('Bad encrypted data');
        }
        $asciiStat = $this->check_ascii($decodedData);
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
     * @param positive-int $length
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
