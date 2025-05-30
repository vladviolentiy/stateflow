<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\Enums\ServicesEnum;
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
        $this->controller = new EmailService(new EmailStorage(
            $this->databaseConnectionFactory->createConnection(ServicesEnum::Id),
        ), $this->info['userId']);
    }

    /**
     * @api
     */
    public function addNewEmail(): JsonResponse
    {
        $emailEncrypted = $this->request->request->getString('emailEncrypted');
        $emailHash = $this->request->request->getString('emailHash');
        $allowAuth = $this->request->request->getBoolean('allowAuth');

        $this->controller->addNewEmail($emailEncrypted, $emailHash, $allowAuth);

        return $this->getEmailList();
    }

    /**
     * @api
     */
    public function editEmail(): JsonResponse
    {
        $itemId = $this->request->request->getInt('itemId');
        $emailEncrypted = $this->request->request->getString('emailEncrypted');
        $emailHash = $this->request->request->getString('emailHash');
        $allowAuth = $this->request->request->getBoolean('allowAuth');

        $this->controller->editItem($itemId, $emailEncrypted, $emailHash, $allowAuth);

        return $this->getEmailList();
    }

    /**
     * @api
     */
    public function getEmailItem(): JsonResponse
    {
        $itemId = $this->request->request->getInt('id');
        $info = $this->controller->getEmailItem($itemId);

        return $info->toResponse();
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
        $id = $this->request->request->getInt('id');
        $this->controller->deleteEmail($id);

        return $this->getEmailList();
    }
}
