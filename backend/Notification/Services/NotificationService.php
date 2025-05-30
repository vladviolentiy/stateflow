<?php

namespace Flow\Notification\Services;

use Flow\Notification\Mappers\NotificationDataMapper;
use Flow\Notification\Resources\NotificationsResource;
use Flow\Notification\Storage\NotificationStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\DatabaseException;
use VladViolentiy\VivaFramework\Validation;

readonly class NotificationService
{
    private readonly NotificationDataMapper $dataMapper;

    /**
     * @param NotificationStorageInterface $notificationStorage
     * @param positive-int $userId
     */
    public function __construct(
        private NotificationStorageInterface $notificationStorage,
        private int $userId,
    ) {
        $this->dataMapper = new NotificationDataMapper($this->notificationStorage, $this->userId);
    }

    /**
     * @return int<0,max>
     * @throws DatabaseException
     */
    public function getNotificationCount(): int
    {
        return $this->notificationStorage->getNotificationCountByUserId($this->userId);
    }

    public function getNotifications(int $limit = 20): NotificationsResource
    {
        Validation::id($limit);
        $info = $this->dataMapper->getNotifications($limit);

        return NotificationsResource::fromState($info);
    }
}
