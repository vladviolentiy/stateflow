<?php

namespace Flow\Core;

use Flow\Id\Controller\AuthController;
use Flow\Id\Storage\Storage;
use Symfony\Component\HttpFoundation\Request;

abstract class WebPrivate extends Web
{
    /** @var array{userId:positive-int,lang:non-empty-string} */
    protected readonly array $info;
    protected readonly Storage $storage;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $token = $this->request->getServer('HTTP_AUTHORIZATION') ?? '';
        $this->storage = new Storage();
        $controller = new AuthController($this->storage);
        $this->info = $controller->checkAuth($token);
    }
}
