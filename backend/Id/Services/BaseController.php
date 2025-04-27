<?php

namespace Flow\Id\Services;

abstract class BaseController
{
    protected readonly string $appToken;

    public function __construct()
    {
        $this->appToken = (string) getenv('APP_TOKEN');
    }
}
