<?php

namespace Flow\Id\Models;

use VladViolentiy\VivaFramework\Validation;

readonly class Password
{
    /** @var non-empty-string  */
    public string $value;

    public function __construct(string $value)
    {
        Validation::hash($value, 96, 'Password value should be a hash');
        $this->value = password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
