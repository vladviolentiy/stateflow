<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Services\Profile\PhonesService;
use Flow\Id\Storage\PhoneStorage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PhoneConfigController extends WebPrivate
{
    private readonly PhonesService $controller;

    /**
     * @api
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new PhonesService(new PhoneStorage(), $this->info['userId']);
    }

    /**
     * @api
     */
    public function get(): Response
    {
        $info = $this->controller->get();

        return new JsonResponse(SuccessResponse::data($info));
    }

    /**
     * @api
     */
    public function addNewPhone(): Response
    {
        $emailEncrypted = $this->req->get('phoneEncrypted');
        $emailHash = $this->req->get('phoneHash');
        $allowAuth = (bool) $this->req->get('allowAuth');

        $this->controller->addNewPhone($emailEncrypted, $emailHash, $allowAuth);

        return $this->get();
    }
}
