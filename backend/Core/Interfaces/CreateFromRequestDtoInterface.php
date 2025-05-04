<?php

namespace Flow\Core\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface CreateFromRequestDtoInterface
{
    public static function createFromRequest(Request $request): DtoInteface;
}
