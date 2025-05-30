<?php

namespace Flow\Notification\Storage;

use Flow\Notification\Dto\NotificationItem;
use VladViolentiy\VivaFramework\Exceptions\DatabaseException;

/**
 * @phpstan-import-type NotificationItemArray from NotificationItem
 */
interface NotificationStorageInterface
{
    /**
     * @param positive-int $userId
     * @return int<0,max>
     * @throws DatabaseException
     */
    public function getNotificationCountByUserId(int $userId): int;

    /**
     * @param positive-int $userId
     * @param positive-int $limit
     * @return list<NotificationItemArray>
     * @throws DatabaseException
     */
    public function getNotificationsForUser(int $userId, int $limit = 20): array;
}
