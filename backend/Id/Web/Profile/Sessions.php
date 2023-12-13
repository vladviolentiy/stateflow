<?php

namespace Flow\Id\Web\Profile;

use Flow\Id\Storage\Storage;
use Flow\Id\Web\Generic;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VladViolentiy\VivaFramework\SuccessResponse;

class Sessions extends Generic
{
    private readonly \Flow\Id\Controller\Profile\Sessions $sessions;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->sessions = new \Flow\Id\Controller\Profile\Sessions($this->storage,$this->info['userId']);
    }

    public function getList():Response
    {
        $sessions = $this->sessions->get();
        return new JsonResponse(SuccessResponse::data($sessions));
    }
}