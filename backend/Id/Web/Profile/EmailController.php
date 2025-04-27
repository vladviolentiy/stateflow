<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Services\Profile\EmailService;
use Flow\Id\Storage\EmailStorage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EmailController extends WebPrivate
{
    private readonly EmailService $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new EmailService(new EmailStorage(), $this->info['userId']);
    }

    /**
     * @api
     */
    public function addNewEmail(): JsonResponse
    {
        $emailEncrypted = $this->req->get('emailEncrypted');
        $emailHash = $this->req->get('emailHash');
        $allowAuth = (bool) $this->req->get('allowAuth');

        $this->controller->addNewEmail($emailEncrypted, $emailHash, $allowAuth);

        return $this->getEmailList();
    }

    /**
     * @api
     */
    public function editEmail(): JsonResponse
    {
        $itemId = (int) $this->req->get('itemId');
        $emailEncrypted = $this->req->get('emailEncrypted');
        $emailHash = $this->req->get('emailHash');
        $allowAuth = (bool) $this->req->get('allowAuth');

        $this->controller->editItem($itemId, $emailEncrypted, $emailHash, $allowAuth);

        return $this->getEmailList();
    }

    /**
     * @api
     */
    public function getEmailItem(): JsonResponse
    {
        $itemId = (int) $this->req->get('id');
        $info = $this->controller->getEmailItem($itemId);

        return new JsonResponse(SuccessResponse::data($info));
    }

    /**
     * @api
     */
    public function getEmailList(): JsonResponse
    {
        $info = $this->controller->getEmailList();

        return new JsonResponse(SuccessResponse::data($info));
    }

    /**
     * @api
     */
    public function deleteEmail(): JsonResponse
    {
        $id = (int) $this->req->get('id');
        $this->controller->deleteEmail($id);

        return $this->getEmailList();
    }
}
