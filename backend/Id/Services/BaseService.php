<?php

namespace Flow\Id\Services;

abstract class BaseService
{
    protected readonly string $appToken;

    public function __construct()
    {
        $this->appToken = (string) getenv('APP_TOKEN');
    }
}
