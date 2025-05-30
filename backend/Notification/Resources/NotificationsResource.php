<?php

namespace Flow\Notification\Resources;

use Flow\Core\Interfaces\ResponseInterface;
use Flow\Notification\Dto\NotificationItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use VladViolentiy\VivaFramework\SuccessResponse;

final readonly class NotificationsResource implements ResponseInterface
{
    /**
     * @param list<NotificationItem> $items
     */
    public function __construct(
        private array $items,
    ) {}

    public function toArray(): array
    {
        return [
            'notifications' => array_map(function (NotificationItem $item) {
                return $item->toArray();
            }, $this->items),
        ];
    }

    /**
     * @param list<NotificationItem> $state
     * @return static
     */
    public static function fromState(array $state): static
    {
        return new static($state);
    }

    public function toResponse(): JsonResponse
    {
        return new JsonResponse(SuccessResponse::data($this->toArray()));
    }
}
