<?php

namespace Flow\Id\Controller\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Services\Profile\PhonesService;
use Flow\Id\Storage\PhoneStorage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    public function get(): JsonResponse
    {
        $info = $this->controller->get();

        return new JsonResponse(SuccessResponse::data($info));
    }

    /**
     * @api
     */
    public function addNewPhone(): JsonResponse
    {
        $emailEncrypted = $this->request->request->getString('phoneEncrypted');
        $emailHash = $this->request->request->getString('phoneHash');
        $allowAuth = $this->request->request->getBoolean('allowAuth');

        $this->controller->addNewPhone($emailEncrypted, $emailHash, $allowAuth);

        return $this->get();
    }
}
