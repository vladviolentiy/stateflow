<?php

namespace Flow\Core\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface CreateFromRequestInterface
{
    public static function createFromRequest(Request $request): DtoInterface;
}
