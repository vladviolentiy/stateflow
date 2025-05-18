<?php

namespace Flow\Id\Model;

readonly class User
{
    public function __construct(
        public int $id,
        public ?string $uuid,
        public ?string $iv,
        public ?string $salt,
        public ?string $fNameEncrypted,
    ) {}
}
