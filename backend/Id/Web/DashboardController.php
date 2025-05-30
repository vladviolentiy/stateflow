<?php

namespace Flow\Id\Web;

use Flow\Core\Enums\ServicesEnum;
use Flow\Id\DTO\CheckAuthDTO;
use Flow\Id\DTO\WriteMetaInfoDTO;
use Flow\Id\Services\Profile\GeneralService;
use Flow\Id\Services\Profile\SessionsService;
use Flow\Id\Storage\SessionStorage;
use Flow\Notification\Services\NotificationService;
use Flow\Notification\Storage\NotificationStorage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Flow\Core\WebPrivate;
use Symfony\Component\HttpFoundation\JsonResponse;

class DashboardController extends WebPrivate
{
    /**
     * @api
     */
    public function checkAuth(): JsonResponse
    {
        $generalController = new GeneralService($this->userStorage);
        $notificationService = new NotificationService(
            new NotificationStorage(
                $this->databaseConnectionFactory,
            ),
            $this->info['userId'],
        );

        return $generalController->enrichUserInfo(
            $this->info,
            CheckAuthDTO::createFromRequest($this->request),
            $notificationService,
        )->toResponse();
    }

    /**
     * @api
     */
    public function getBasicInfo(): JsonResponse
    {
        $generalController = new GeneralService($this->userStorage);
        $data = $generalController->getBasicInfo($this->info['userId']);

        return new JsonResponse(SuccessResponse::data($data));
    }

    /**
     * @api
     */
    public function writeMetaInfo(): JsonResponse
    {
        $AuthController = new SessionsService(new SessionStorage(
            $this->databaseConnectionFactory->createConnection(ServicesEnum::Id),
        ), $this->info['userId']);
        $AuthController->writeHashInfo(WriteMetaInfoDTO::createFromRequest($this->request), $this->info['sessionId']);

        return new JsonResponse([], 204);
    }
}
