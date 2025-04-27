<?php

namespace Flow\Id\Models;

use VladViolentiy\VivaFramework\Validation;

class RsaPublicKey
{
    /** @var non-empty-string */
    public string $value;

    public function __construct(string $value, string $field = '')
    {
        Validation::nonEmpty($value);
        \Flow\Core\Validation::RSAPublicKey()->validate($value, $field);
        $this->value = $value;
    }
}
