<?php

namespace Flow\Id\Model;

use Flow\Core\Interfaces\ModelInterface;

final readonly class UserEmail implements ModelInterface
{
    public function __construct(
        public int $id,
        public ?int $userId,
        public ?string $hash,
        public ?string $emailEncrypted,
        public ?bool $allowAuth,
        public ?User $user,
    ) {}
}
