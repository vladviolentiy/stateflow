<?php

namespace Flow\Notification\Mappers;

use Flow\Notification\Dto\NotificationItem;
use Flow\Notification\Storage\NotificationStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\DatabaseException;

readonly class NotificationDataMapper
{
    /**
     * @param NotificationStorageInterface $storage
     * @param positive-int $userId
     */
    public function __construct(
        private NotificationStorageInterface $storage,
        private int $userId,
    ) {}

    /**
     * @param positive-int $limit
     * @return list<NotificationItem>
     * @throws DatabaseException
     */
    public function getNotifications(int $limit = 20): array
    {
        $info = $this->storage->getNotificationsForUser($this->userId, $limit);

        return array_map(function (array $notification) {
            return NotificationItem::fromState($notification);
        }, $info);
    }
}
