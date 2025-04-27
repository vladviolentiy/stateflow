<?php

namespace Flow\Id\Models;

use VladViolentiy\VivaFramework\Validation;

readonly class EncryptedData
{
    /** @var non-empty-string  */
    public string $value;

    public function __construct(string $value, string $field = '')
    {
        Validation::nonEmpty($value);
        \Flow\Core\Validation::encryptedData($value, $field);
        $this->value = $value;
    }
}
