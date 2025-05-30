<?php

namespace Flow\Notification\Dto;

use Flow\Core\Interfaces\DtoInterface;

/**
 * @phpstan-type NotificationItemArray array{type:non-empty-string, message: non-empty-string}
 */
final readonly class NotificationItem implements DtoInterface
{
    /**
     * @param non-empty-string $message
     * @param non-empty-string $type
     */
    public function __construct(
        private string $message,
        private string $type,
    ) {}

    /**
     * @return NotificationItemArray
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
        ];
    }

    /**
     * @param NotificationItemArray $state
     * @return static
     */
    public static function fromState(array $state): static
    {
        return new static($state['message'], $state['type']);
    }
}
