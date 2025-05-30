<?php

namespace Flow\Notification\Model;

readonly class Notification
{
    public function __construct(
        public int $id,
        public ?int $userId,
        public ?string $type,
        public ?string $message,
        public ?string $createdAt,
        public ?string $updatedAt,
        public ?string $readAt,
    ) {}
}
