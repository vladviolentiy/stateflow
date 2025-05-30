<?php

namespace Flow\Notification\Controllers;

use Flow\Core\WebPrivate;
use Flow\Notification\Services\NotificationService;
use Flow\Notification\Storage\NotificationStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends WebPrivate
{
    private readonly NotificationService $notificationService;

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
        $this->notificationService = $this->getService();
    }

    private function getService(): NotificationService
    {
        return new NotificationService(
            new NotificationStorage($this->databaseConnectionFactory),
            $this->info['userId'],
        );
    }

    /**
     * @api
     */
    public function getNotifications(): JsonResponse
    {
        $limit = $this->request->query->getInt('limit', 20);

        return $this->notificationService->getNotifications($limit)->toResponse();
    }
}
