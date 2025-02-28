<?php

namespace Flow\Core;

use Error;
use Exception;
use Flow\Id\Web\Auth;
use Flow\Id\Web\Dashboard;
use Flow\Id\Web\Profile\Email;
use Flow\Id\Web\Profile\Phones;
use Flow\Id\Web\Profile\Sessions;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Route
{
    /** @var list<array{route:non-empty-string,class:class-string,method:non-empty-string}> */
    private static array $list = [
        [
            'route' => '/api/id/checkIssetClient',
            'class' => Auth::class,
            'method' => 'checkIssetClient',
        ],
        [
            'route' => '/api/id/register',
            'class' => Auth::class,
            'method' => 'register',
        ],
        [
            'route' => '/api/id/passwordAuth',
            'class' => Auth::class,
            'method' => 'passwordAuth',
        ],
        [
            'route' => '/api/id/checkAuth',
            'class' => Dashboard::class,
            'method' => 'checkAuth',
        ],
        [
            'route' => '/api/id/killSession',
            'class' => Sessions::class,
            'method' => 'killSession',
        ],
        [
            'route' => '/api/id/session/get',
            'class' => Sessions::class,
            'method' => 'get',
        ],
        [
            'route' => '/api/id/writeMeta',
            'class' => Dashboard::class,
            'method' => 'writeMetaInfo',
        ],
        [
            'route' => '/api/id/email/get',
            'class' => Email::class,
            'method' => 'getEmailList',
        ],
        [
            'route' => '/api/id/email/getItem',
            'class' => Email::class,
            'method' => 'getEmailItem',
        ],
        [
            'route' => '/api/id/email/add',
            'class' => Email::class,
            'method' => 'addNewEmail',
        ],
        [
            'route' => '/api/id/email/delete',
            'class' => Email::class,
            'method' => 'deleteEmail',
        ],
        [
            'route' => '/api/id/phone/get',
            'class' => Phones::class,
            'method' => 'get',
        ],
        [
            'route' => '/api/id/phone/getItem',
            'class' => Phones::class,
            'method' => 'getItem',
        ],
        [
            'route' => '/api/id/phone/add',
            'class' => Phones::class,
            'method' => 'add',
        ],
        [
            'route' => '/api/id/phone/delete',
            'class' => Phones::class,
            'method' => 'delete',
        ],
    ];

    public static function handle(): void
    {
        /** @var non-empty-string $require */
        $require = $_SERVER['REQUEST_URI'];
        $require = explode('?', $require)[0];

        $routes = Route::$list;

        $dotenv = new Dotenv();
        $dotenv->bootEnv(__DIR__ . '/../../.env');
        $request = Request::createFromGlobals();

        try {

            foreach ($routes as $route) {
                if ($route['route'] === $require) {
                    $method = $route['method'];
                    $class = new $route['class']($request);
                    /** @var Response $response */
                    $response = $class->$method();
                    $response->send();

                    return;
                }
            }

            throw new Exception('Route not found');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
                'text' => $e->getMessage(),
            ]);
        } catch (Error $e) {
            echo json_encode([
                'success' => false,
                'code' => 500,
                'trace' => $e->getTrace(),
                'text' => $e->getMessage(),
            ]);
        }

    }
}
