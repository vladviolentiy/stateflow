<?php

namespace Flow\Id\Model;

use Flow\Core\Interfaces\ModelInterface;

final readonly class Session implements ModelInterface
{
    public function __construct(
        public int $id,
        public ?string $hash,
        public ?int $userId,
        public ?User $user,
    ) {}
}
