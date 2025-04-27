<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\PrivateController;
use Flow\Id\Services\Profile\EmailService;
use Flow\Id\Storage\EmailStorage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Email extends PrivateController
{
    private readonly EmailService $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new EmailService(new EmailStorage(), $this->info['userId']);
    }

    public function addNewEmail(): JsonResponse
    {
        $emailEncrypted = $this->request->get('emailEncrypted');
        $emailHash = $this->request->get('emailHash');
        $allowAuth = (bool) $this->request->get('allowAuth');

        $this->controller->addNewEmail($emailEncrypted, $emailHash, $allowAuth);

        return $this->getEmailList();
    }

    public function editEmail(): JsonResponse
    {
        $itemId = (int) $this->request->get('itemId');
        $emailEncrypted = $this->request->get('emailEncrypted');
        $emailHash = $this->request->get('emailHash');
        $allowAuth = (bool) $this->request->get('allowAuth');

        $this->controller->editItem($itemId, $emailEncrypted, $emailHash, $allowAuth);

        return $this->getEmailList();
    }

    public function getEmailItem(): JsonResponse
    {
        $itemId = (int) $this->request->get('id');
        $info = $this->controller->getEmailItem($itemId);

        return new JsonResponse(SuccessResponse::data($info));
    }

    public function getEmailList(): JsonResponse
    {
        $info = $this->controller->getEmailList();

        return new JsonResponse(SuccessResponse::data($info));
    }

    public function deleteEmail(): JsonResponse
    {
        $id = (int) $this->request->get('id');
        $this->controller->deleteEmail($id);

        return $this->getEmailList();
    }
}
