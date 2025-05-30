<?php

namespace Flow\Core;

use Symfony\Component\HttpFoundation\Request;

abstract class Web
{
    protected readonly DatabaseConnectionFactory $databaseConnectionFactory;

    public function __construct(
        protected readonly Request $request,
    ) {
        $this->databaseConnectionFactory = new DatabaseConnectionFactory();
    }
}
