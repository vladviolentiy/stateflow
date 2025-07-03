<?php

namespace Flow\Id\Controller;

use Flow\Core\Enums\ServicesEnum;
use Flow\Core\Web;
use Flow\Id\DTO\Factories\RegisterClientFactory;
use Flow\Id\Services\AuthService;
use Flow\Id\Storage\SessionStorage;
use Flow\Id\Storage\UserStorage;
use OpenApi\Attributes as OA;
use VladViolentiy\VivaFramework\SuccessResponse;
use Flow\Id\Enums\AuthMethods;
use Flow\Id\Enums\AuthVia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Web
{
    private readonly AuthService $controller;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $conn = $this->databaseConnectionFactory->createConnection(ServicesEnum::Id);

        $this->controller = new AuthService(new UserStorage($conn), new SessionStorage($conn));
    }

    /**
     * @api
     */
    public function checkIssetClient(): JsonResponse
    {
        $phone = $this->request->request->getString('authString');
        $type = $this->request->request->getString('type');
        $data = $this->controller->getAuthDataForUser($phone, AuthMethods::from($type));

        return new JsonResponse(SuccessResponse::data($data));
    }

    /**
     * @api
     */
    #[OA\Post(
        path: '/api/id/auth/password',
        description: 'Authenticates a user using phone/email/UUID and password. Returns auth token and crypto parameters.',
        summary: 'Authenticate user with password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['authString', 'authStringType', 'password'],
                properties: [
                    new OA\Property(
                        property: 'authString',
                        description: 'Phone, Email or UUID of the user',
                        type: 'string',
                        example: 'eebea277-8b3b-4921-88cf-08e2a35af467',
                    ),
                    new OA\Property(
                        property: 'authStringType',
                        description: 'Type of auth string',
                        type: 'string',
                        enum: ['email', 'phone', 'uuid'],
                        example: 'email',
                    ),
                    new OA\Property(
                        property: 'password',
                        description: 'User\'s password (already hashed on client side)',
                        type: 'string',
                        example: 'hashed_password_123',
                    ),
                ],
            ),
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful authentication',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthResource'),
            ),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 401, description: 'Authentication failed'),
            new OA\Response(response: 500, description: 'Internal server error'),
        ],
    )]
    public function passwordAuth(): JsonResponse
    {
        $phone = $this->request->request->getString('authString');
        $type = $this->request->request->getString('authStringType');
        $authString = $this->request->request->getString('password');
        $data = $this->controller->auth($phone, AuthMethods::from($type), AuthVia::Password, $authString);

        return $data->toResponse();
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
    public function register(): JsonResponse
    {
        $creds = RegisterClientFactory::createFromRequest($this->request);

        $uuid = $this->controller->createNewUser($creds);

        return $uuid->toResponse();
    }
}
