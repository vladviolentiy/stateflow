<?php

namespace Flow\Id\ValueObject;

use VladViolentiy\VivaFramework\Validation;

readonly class PrivateKey
{
    /** @var non-empty-string  */
    public string $value;

    public function __construct(string $value)
    {
        Validation::nonEmpty($value, 'Password value should not be empty');
        \Flow\Core\Validation::encryptedData($value);
        $this->value = $value;
    }
}
