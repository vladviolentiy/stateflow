<?php

namespace Flow\Core;

use Error;
use Exception;
use Flow\Id\Controller\AuthController;
use Flow\Id\Controller\DashboardController;
use Flow\Id\Controller\Profile\EmailController;
use Flow\Id\Controller\Profile\PhoneConfigController;
use Flow\Id\Controller\Profile\ProfileController;
use Flow\Id\Controller\Profile\SessionsController;
use OpenApi\Attributes\Info;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Info(version: '1.0', title: 'Stateflow project API')]
class Application
{
    /** @var list<array{route:non-empty-string,method:"GET"|"POST"|"PUT"|"PATCH"|"DELETE",class:class-string,handler:non-empty-string}> */
    private static array $list = [
        [
            'route' => '/api/id/checkIssetClient',
            'class' => AuthController::class,
            'method' => 'POST',
            'handler' => 'checkIssetClient',
        ],
        [
            'route' => '/api/id/register',
            'class' => AuthController::class,
            'method' => 'POST',
            'handler' => 'register',
        ],
        [
            'route' => '/api/id/passwordAuth',
            'class' => AuthController::class,
            'method' => 'POST',
            'handler' => 'passwordAuth',
        ],
        [
            'route' => '/api/id/checkAuth',
            'class' => DashboardController::class,
            'method' => 'POST',
            'handler' => 'checkAuth',
        ],
        [
            'route' => '/api/id/killSession',
            'class' => SessionsController::class,
            'method' => 'POST',
            'handler' => 'killSession',
        ],
        [
            'route' => '/api/id/session/get',
            'class' => SessionsController::class,
            'method' => 'POST',
            'handler' => 'get',
        ],
        [
            'route' => '/api/id/writeMeta',
            'class' => DashboardController::class,
            'method' => 'POST',
            'handler' => 'writeMetaInfo',
        ],
        [
            'route' => '/api/id/email/get',
            'class' => EmailController::class,
            'method' => 'GET',
            'handler' => 'getEmailList',
        ],
        [
            'route' => '/api/id/email/getItem',
            'class' => EmailController::class,
            'method' => 'GET',
            'handler' => 'getEmailItem',
        ],
        [
            'route' => '/api/id/email/add',
            'class' => EmailController::class,
            'method' => 'POST',
            'handler' => 'addNewEmail',
        ],
        [
            'route' => '/api/id/email/delete',
            'class' => EmailController::class,
            'method' => 'DELETE',
            'handler' => 'deleteEmail',
        ],
        [
            'route' => '/api/id/phone/get',
            'class' => PhoneConfigController::class,
            'method' => 'GET',
            'handler' => 'get',
        ],
        [
            'route' => '/api/id/phone/getItem',
            'class' => PhoneConfigController::class,
            'method' => 'GET',
            'handler' => 'getItem',
        ],
        [
            'route' => '/api/id/phone/add',
            'class' => PhoneConfigController::class,
            'method' => 'POST',
            'handler' => 'add',
        ],
        [
            'route' => '/api/id/phone/delete',
            'class' => PhoneConfigController::class,
            'method' => 'DELETE',
            'handler' => 'delete',
        ],
        [
            'route' => '/api/id/profile/changePassword',
            'class' => ProfileController::class,
            'method' => 'PUT',
            'handler' => 'changePassword',
        ],
    ];

    public static function load(): void
    {
        $dotenv = new Dotenv();
        $dotenv->bootEnv(__DIR__ . '/../../.env');
        $request = Request::createFromGlobals();
        $response = self::handle($request);
        $response->send();
    }

    public static function handle(
        Request $request,
    ): JsonResponse {
        /** @var non-empty-string $uri */
        $uri = $request->server->get('REQUEST_URI');
        $require = explode('?', $uri)[0];
        $routes = Application::$list;
        $requestMethod = $request->getMethod();
        try {
            foreach ($routes as $route) {
                if ($route['method'] === $requestMethod && $route['route'] === $require) {
                    $method = $route['handler'];
                    $class = new $route['class']($request);
                    /** @var JsonResponse $response */
                    $response = $class->$method();

                    return $response;
                }
            }

            throw new Exception('Route not found');
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
                'text' => $e->getMessage(),
            ]);
        } catch (Error $e) {
            return new JsonResponse([
                'success' => false,
                'code' => 500,
                'trace' => $e->getTrace(),
                'text' => $e->getMessage(),
            ]);
        }

    }
}
