<?php

namespace Flow\Id\Model;

readonly class UserPhone
{
    public function __construct(
        public int $id,
        public ?int $userId,
        public ?string $hash,
        public ?string $phoneEncrypted,
        public ?bool $allowAuth,
        public ?bool $deleted,
        public ?User $user,
    ) {}
}
