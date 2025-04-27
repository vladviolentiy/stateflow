<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\PrivateController;
use Flow\Id\Services\Profile\PhonesService;
use Flow\Id\Storage\PhoneStorage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Phones extends PrivateController
{
    private readonly PhonesService $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new PhonesService(new PhoneStorage(), $this->info['userId']);
    }

    public function get(): Response
    {
        $info = $this->controller->get();

        return new JsonResponse(SuccessResponse::data($info));
    }

    public function addNewPhone(): Response
    {
        $emailEncrypted = $this->request->get('phoneEncrypted');
        $emailHash = $this->request->get('phoneHash');
        $allowAuth = (bool) $this->request->get('allowAuth');

        $this->controller->addNewPhone($emailEncrypted, $emailHash, $allowAuth);

        return $this->get();
    }
}
