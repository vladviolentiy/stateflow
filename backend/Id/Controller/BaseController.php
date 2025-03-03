<?php

namespace Flow\Id\Controller;

use Flow\Id\Storage\StorageInterface;

class BaseController
{
    protected readonly StorageInterface $storage;
    protected readonly string $appToken;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->appToken = (string) getenv('APP_TOKEN');
    }
}
