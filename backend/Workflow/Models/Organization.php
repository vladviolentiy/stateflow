<?php

namespace Flow\Workflow\Models;

use Flow\Core\Interfaces\ModelInterface;

final readonly class Organization implements ModelInterface
{
    public function __construct(
        public int $id,
        public int $ownerId,
        public string $iv,
        public string $salt,
        public string $name,
    ) {}
}
