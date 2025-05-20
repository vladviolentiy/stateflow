<?php

namespace Flow\Id\Model;

use Flow\Core\Interfaces\ModelInterface;

final readonly class User implements ModelInterface
{
    public function __construct(
        public int $id,
        public ?string $uuid,
        public ?string $iv,
        public ?string $salt,
        public ?string $fNameEncrypted,
    ) {}
}
