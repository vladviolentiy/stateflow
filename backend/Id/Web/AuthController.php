<?php

namespace Flow\Id\Web;

use Flow\Core\Web;
use Flow\Id\DTO\RegisterClientDTO;
use Flow\Id\Services\AuthService;
use Flow\Id\Storage\SessionStorage;
use Flow\Id\Storage\UserStorage;
use VladViolentiy\VivaFramework\SuccessResponse;
use Flow\Id\Enums\AuthMethods;
use Flow\Id\Enums\AuthVia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Web
{
    private readonly AuthService $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controller = new AuthService(new UserStorage(), new SessionStorage());
    }

    /**
     * @api
     */
    public function checkIssetClient(): Response
    {
        $phone = $this->req->get('authString');
        $type = $this->req->get('type');
        $data = $this->controller->getAuthDataForUser($phone, AuthMethods::from($type));

        return new JsonResponse(SuccessResponse::data($data));
    }

    /**
     * @api
     */
    public function passwordAuth(): Response
    {
        $phone = $this->req->get('authString');
        $type = $this->req->get('authStringType');
        $authString = $this->req->get('password');
        $data = $this->controller->auth($phone, AuthMethods::from($type), AuthVia::Password, $authString);

        return new JsonResponse(SuccessResponse::data($data->toArray()));
    }

    /**
     * @api
     */
    public function register(): Response
    {
        $creds = RegisterClientDTO::createFrom($this->request);

        $uuid = $this->controller->createNewUser($creds);

        return new JsonResponse(SuccessResponse::data($uuid->toArray()));
    }
}
