<?php

namespace Flow\Id;

use Flow\Core\Exceptions\ValidationException;
use Flow\Core\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebPrivate extends Web
{
    /** @var array{userId:positive-int} */
    private array $info;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $token = $this->request->get("token");
        $this->info = $this->controller->checkAuth($token);
    }

    public function checkAuth():Response{
        return new JsonResponse(SuccessResponse::data($this->info));
    }

    public function addNewEmail():Response{
        $emailEncrypted = $this->request->get("emailEncrypted");
        $emailHash = $this->request->get("emailHash");
        $allowAuth = (bool)$this->request->get("allowAuth");

        if($emailHash=="" or $emailEncrypted==="") throw new ValidationException();

        $this->controller->addNewEmail($this->info['userId'],$emailEncrypted,$emailHash,$allowAuth);
        return $this->getEmailList();
    }

    public function editEmail():Response{
        $itemId = (int)$this->request->get("itemId");
        $emailEncrypted = $this->request->get("emailEncrypted");
        $emailHash = $this->request->get("emailHash");
        $allowAuth = (bool)$this->request->get("allowAuth");

        $this->controller->editItem($this->info['userId'],$itemId,$emailEncrypted,$emailHash,$allowAuth);
        return $this->getEmailList();
    }


    public function getEmailItem():Response{
        $itemId = (int)$this->request->get("id");
        $info = $this->controller->getEmailItem($this->info['userId'],$itemId);
        return new JsonResponse(SuccessResponse::data($info));
    }

    public function getEmailList():Response{
        $info = $this->controller->getEmailList($this->info['userId']);
        return new JsonResponse(SuccessResponse::data($info));
    }

    public function deleteEmail():Response{
        $id = (int)$this->request->get("id");
        $this->controller->deleteEmail($this->info['userId'],$id);
        return $this->getEmailList();
    }
}