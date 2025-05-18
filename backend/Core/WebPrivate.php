<?php

namespace Flow\Core;

use Flow\Id\Services\AuthService;
use Flow\Id\Storage\SessionStorage;
use Flow\Id\Storage\UserStorage;
use Symfony\Component\HttpFoundation\Request;

abstract class WebPrivate extends Web
{
    /** @var array{userId:positive-int,lang:non-empty-string, sessionId: positive-int} */
    protected readonly array $info;
    protected readonly UserStorage $storage;
    protected readonly SessionStorage $sessionStorage;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->storage = new UserStorage();
        $this->sessionStorage = new SessionStorage();
        $controller = new AuthService($this->storage, $this->sessionStorage);
        $this->info = $controller->checkAuth($request->server->getString('HTTP_AUTHORIZATION'));
    }
}
