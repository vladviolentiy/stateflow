<?php

namespace Flow\Id\Web;

use Flow\Id\Services\Profile\General;
use Flow\Id\Services\Profile\SessionsService;
use Flow\Id\Storage\SessionStorage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Flow\Core\WebPrivate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Dashboard extends WebPrivate
{
    /**
     * @api
     */
    public function checkAuth(): JsonResponse
    {
        return new JsonResponse(SuccessResponse::data($this->info));
    }

    /**
     * @api
     */
    public function getBasicInfo(): JsonResponse
    {
        $generalController = new General($this->storage, $this->info['userId']);
        $data = $generalController->getBasicInfo();

        return new JsonResponse(SuccessResponse::data($data));

    }

    /**
     * @api
     */
    public function writeMetaInfo(): JsonResponse
    {
        $token = $this->req->getServer('HTTP_AUTHORIZATION') ?? '';
        $ip = $this->req->get('ip');
        $ua = $this->req->get('ua');
        $al = $this->req->get('al');
        $ae = $this->req->get('ae');
        $lastSeen = $this->req->get('lastSeen');
        $AuthController = new SessionsService(new SessionStorage(), $this->info['userId']);
        $AuthController->writeHashInfo(
            $token,
            $ip,
            $ua,
            $ae,
            $al,
            $lastSeen,
        );

        return new JsonResponse([], 204);
    }
}
