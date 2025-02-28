<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Controller\Profile\PhonesController;
use VladViolentiy\VivaFramework\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Phones extends WebPrivate
{
    private readonly PhonesController $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new PhonesController($this->storage, $this->info['userId']);
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
