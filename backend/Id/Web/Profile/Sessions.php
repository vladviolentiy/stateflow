<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\PrivateController;
use Flow\Id\Services\Profile\SessionsService;
use Flow\Id\Storage\SessionStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VladViolentiy\VivaFramework\SuccessResponse;

class Sessions extends PrivateController
{
    private readonly SessionsService $sessions;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->sessions = new SessionsService(new SessionStorage(), $this->info['userId']);
    }

    public function get(): Response
    {
        $sessions = $this->sessions->get();

        return new JsonResponse(SuccessResponse::data($sessions));
    }

    public function killSession(): Response
    {
        $hash = $this->request->get('hash');
        $returnAvailable = (bool) $this->request->get('returnAvailable');
        $info = $this->sessions->killSession($hash, $returnAvailable);
        if ($info === null) {
            return new JsonResponse(SuccessResponse::null());
        } else {
            return new JsonResponse(SuccessResponse::data($info));

        }
    }
}
