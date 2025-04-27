<?php

namespace Flow\Core;

use Symfony\Component\HttpFoundation\Request;
use VladViolentiy\VivaFramework\Req;

abstract class Web
{
    protected readonly Req $req;

    public function __construct(
        protected readonly Request $request,
    ) {
        $this->req = new Req($request);
    }
}
