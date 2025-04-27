<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Services\Profile\ProfileService;
use Flow\Id\Storage\UserStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends WebPrivate
{
    private readonly ProfileService $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new ProfileService(new UserStorage(), $this->info['userId']);
    }

    /**
     * @api
     */
    public function changePassword(): JsonResponse
    {
        $password = $this->req->get('password');
        $encryptedKey = $this->req->get('encryptedKey');

        $this->controller->updatePassword($encryptedKey, $password);

        return new JsonResponse(status: 204);
    }
}
