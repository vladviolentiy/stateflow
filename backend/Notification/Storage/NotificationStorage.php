<?php

namespace Flow\Notification\Storage;

use Flow\Core\DatabaseConnectionFactory;
use Flow\Core\Enums\ServicesEnum;
use Flow\Core\MysqlAccessor;

class NotificationStorage implements NotificationStorageInterface
{
    private readonly MysqlAccessor $accessor;

    public function __construct(DatabaseConnectionFactory $factory)
    {
        $this->accessor = $factory->getAccessor(ServicesEnum::Notification);
    }

    public function getNotificationCountByUserId(int $userId): int
    {
        /** @var array{count: int<0,max>} $data */
        $data = $this->accessor->executeQuery('SELECT count(*) FROM notifications WHERE userId = ? and readAt is null', [$userId])->fetch_array(MYSQLI_ASSOC);

        return $data['count'];
    }

    public function getNotificationsForUser(int $userId, int $limit = 20): array
    {
        /** @var list<array{type:non-empty-string, message: non-empty-string}> $data */
        $data = $this->accessor->executeQuery('SELECT type, message FROM notifications WHERE userId=? ORDER BY id DESC  LIMIT ?', [$userId, $limit])->fetch_all(MYSQLI_ASSOC);

        return $data;
    }
}
