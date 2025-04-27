<?php

namespace Flow\Core\Validations;

interface ValidationInterface
{
    public function validate(string $input, string $field = ''): true;
}
