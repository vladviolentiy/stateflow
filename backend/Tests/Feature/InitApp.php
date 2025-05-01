<?php

namespace Flow\Tests\Feature;

use Flow\Core\Route;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InitApp
{
    public function __construct()
    {
        self::initTestEnv();
    }

    /**
     * @param 'GET'|'POST'|'PUT' $method
     * @param non-empty-string $endpoint
     * @param array<mixed> $query
     * @return Response
     * @throws \JsonException
     */
    final public function sendQuery(string $method, string $endpoint, array $query): Response
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [
                'REQUEST_METHOD' => $method,
                'REQUEST_URI' => $endpoint,
            ],
            json_encode($query, JSON_THROW_ON_ERROR),
        );

        return Route::handle($request);
    }

    public static function initTestEnv(): void
    {
        $dotenv = new Dotenv();
        $dotenv->usePutenv();
        $dotenv->loadEnv(__DIR__ . '/../../../.env.testing');
    }
}
