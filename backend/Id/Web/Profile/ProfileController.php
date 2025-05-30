<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Services\Profile\ProfileService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends WebPrivate
{
    private readonly ProfileService $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new ProfileService($this->userStorage, $this->info['userId']);
    }

    /**
     * @api
     */
    public function changePassword(): JsonResponse
    {
        $password = $this->request->request->getString('password');
        $encryptedKey = $this->request->request->getString('encryptedKey');

        $this->controller->updatePassword($encryptedKey, $password);

        return new JsonResponse(status: 204);
    }
}
