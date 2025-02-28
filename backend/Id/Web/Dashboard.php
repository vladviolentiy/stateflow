<?php

namespace Flow\Id\Web;

use Flow\Id\Controller\Profile\General;
use VladViolentiy\VivaFramework\SuccessResponse;
use Flow\Core\WebPrivate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Dashboard extends WebPrivate
{
    public function checkAuth(): Response
    {
        return new JsonResponse(SuccessResponse::data($this->info));
    }

    public function getBasicInfo(): Response
    {
        $generalController = new General($this->storage, $this->info['userId']);
        $data = $generalController->getBasicInfo();

        return new JsonResponse(SuccessResponse::data($data));

    }

    public function writeMetaInfo(): Response
    {
        $token = $this->request->getServer('HTTP_AUTHORIZATION') ?? '';
        $ip = $this->request->get('ip');
        $ua = $this->request->get('ua');
        $al = $this->request->get('al');
        $ae = $this->request->get('ae');
        $lastSeen = $this->request->get('lastSeen');
        $AuthController = new \Flow\Id\Controller\AuthController($this->storage);
        $AuthController->writeHashInfo(
            $token,
            $ip,
            $ua,
            $ae,
            $al,
            $lastSeen,
        );

        return new JsonResponse(SuccessResponse::null());
    }
}
