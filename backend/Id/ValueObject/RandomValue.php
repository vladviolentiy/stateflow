<?php

namespace Flow\Id\ValueObject;

final readonly class RandomValue
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
