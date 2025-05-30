<?php

namespace Flow\Core;

use Flow\Core\Enums\ServicesEnum;
use Flow\Id\Services\AuthService;
use Flow\Id\Storage\SessionStorage;
use Flow\Id\Storage\UserStorage;
use Symfony\Component\HttpFoundation\Request;

abstract class WebPrivate extends Web
{
    /** @var array{userId:positive-int,lang:non-empty-string, sessionId: positive-int} */
    protected readonly array $info;
    protected readonly UserStorage $userStorage;
    protected readonly SessionStorage $sessionStorage;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $conn = $this->databaseConnectionFactory->createConnection(ServicesEnum::Id);
        $this->userStorage = new UserStorage($conn);
        $this->sessionStorage = new SessionStorage($conn);
        $controller = new AuthService($this->userStorage, $this->sessionStorage);
        $this->info = $controller->checkAuth($request->server->getString('HTTP_AUTHORIZATION'));
    }
}
