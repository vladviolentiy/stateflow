<?php

namespace Flow\Id\Web;

use Flow\Core\Web;
use Flow\Id\DTO\Factories\RegisterClientDtoFactory;
use Flow\Id\Services\AuthService;
use Flow\Id\Storage\SessionStorage;
use Flow\Id\Storage\UserStorage;
use OpenApi\Attributes as OA;
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
    #[OA\Post(
        path: '/api/id/register',
        description: 'Creates a new user with the provided credentials',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterClientDTO'),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful registration',
                content: new OA\JsonContent(ref: '#/components/schemas/RegisterResource'),
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 500, description: 'Internal server error'),
        ],
    )]
    public function register(): Response
    {
        $creds = RegisterClientDtoFactory::createFromRequest($this->request);

        $uuid = $this->controller->createNewUser($creds);

        return new JsonResponse(SuccessResponse::data($uuid->toArray()));
    }
}
