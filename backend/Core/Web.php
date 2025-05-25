<?php

namespace Flow\Core;

use Symfony\Component\HttpFoundation\Request;

abstract class Web
{
    public function __construct(
        protected readonly Request $request,
    ) {}
}
