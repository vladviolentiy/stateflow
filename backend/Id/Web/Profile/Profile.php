<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\PrivateController;
use Flow\Id\Services\Profile\ProfileService;
use Flow\Id\Storage\UserStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Profile extends PrivateController
{
    private readonly ProfileService $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new ProfileService(new UserStorage(), $this->info['userId']);
    }

    public function changePassword(): JsonResponse
    {
        $password = $this->request->get('password');
        $encryptedKey = $this->request->get('encryptedKey');

        $this->controller->updatePassword($encryptedKey, $password);

        return new JsonResponse(status: 204);
    }
}
