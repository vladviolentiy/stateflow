<?php

namespace Flow\Id\Web;

use Flow\Core\Web;
use Flow\Id\Storage\Storage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Flow\Id\Enums\AuthMethods;
use Flow\Id\Enums\AuthVia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth extends Web
{
    private readonly \Flow\Id\Controller\AuthController $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new \Flow\Id\Controller\AuthController(new Storage());
    }

    public function checkIssetClient(): Response
    {
        $phone = $this->request->get('authString');
        $type = $this->request->get('type');
        $data = $this->controller->getAuthDataForUser($phone, AuthMethods::from($type));

        return new JsonResponse(SuccessResponse::data($data));
    }

    public function passwordAuth(): Response
    {
        $phone = $this->request->get('authString');
        $type = $this->request->get('authStringType');
        $authString = $this->request->get('password');
        $data = $this->controller->auth($phone, AuthMethods::from($type), AuthVia::Password, $authString);

        return new JsonResponse(SuccessResponse::data($data));
    }

    public function register(): Response
    {
        $password = $this->request->get('password');
        $iv = $this->request->get('iv');
        $salt = $this->request->get('salt');
        $publicKey = $this->request->get('publicKey');
        $privateKey = $this->request->get('encryptedPrivateKey');
        $fNameEncrypted = $this->request->get('fNameEncrypted');
        $lNameEncrypted = $this->request->get('lNameEncrypted');
        $bDayEncrypted = $this->request->get('bDayEncrypted');
        $hash = $this->request->get('hash');
        $uuid = $this->controller->createNewUser($password, $iv, $salt, $publicKey, $privateKey, $fNameEncrypted, $lNameEncrypted, $bDayEncrypted, $hash);

        return new JsonResponse(SuccessResponse::data([
            'uuid' => $uuid->toString(),
        ]));
    }
}
