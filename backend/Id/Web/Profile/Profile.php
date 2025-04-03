<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Controller\Profile\ProfileController;
use Flow\Id\Storage\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Profile extends WebPrivate
{
    private readonly ProfileController $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new ProfileController(new Storage(), $this->info['userId']);
    }

    public function changePassword(): JsonResponse
    {
        $password = $this->request->get('password');
        $encryptedKey = $this->request->get('encryptedKey');

        $this->controller->updatePassword($encryptedKey, $password);

        return new JsonResponse(status: 204);
    }
}
