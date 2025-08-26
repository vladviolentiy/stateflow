<?php

namespace Flow\Core\Validations;

use Flow\Core\Services\TextStatService;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;
use VladViolentiy\VivaFramework\Validation;

final class EncryptedDataValidator implements ValidationInterface
{
    private const float ASCII_THRESHOLD = 0.38;
    private const float ENTROPY_THRESHOLD = 0.9;

    public function validate(string $input, string $field = ''): true
    {
        Validation::nonEmpty($input, sprintf('Input data is empty in %s.', $field));
        $decodedData = base64_decode($input, true);
        if (empty($decodedData)) {
            throw new ValidationException(sprintf('Invalid base64 data in %s', $field));
        }
        $textStatService = new TextStatService($decodedData);

        if ($textStatService->length % 16 !== 0) {
            throw new ValidationException(sprintf('Invalid encrypted data in %s', $field));
        }

        $entropy = $textStatService->calculateEntropy();
        $maxEntropyValue = log(count($textStatService->freqTable), 2);
        if ($maxEntropyValue == 0 || $entropy === 0.0 || $entropy < $maxEntropyValue * self::ENTROPY_THRESHOLD) {
            throw new ValidationException(sprintf('Bad encrypted data in %s', $field));
        }

        $asciiStat = $textStatService->getAsciiProbability();
        if ($asciiStat > self::ASCII_THRESHOLD) {
            throw new ValidationException(sprintf('Bad encrypted data (ascii) in %s', $field));
        }

        return true;
    }
}
