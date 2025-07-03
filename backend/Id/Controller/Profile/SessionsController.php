<?php

namespace Flow\Id\Controller\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Services\Profile\SessionsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VladViolentiy\VivaFramework\SuccessResponse;

class SessionsController extends WebPrivate
{
    private readonly SessionsService $sessions;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->sessions = new SessionsService($this->sessionStorage, $this->info['userId']);
    }

    /**
     * @api
     */
    public function get(): JsonResponse
    {
        $sessions = $this->sessions->get();

        return new JsonResponse(SuccessResponse::data($sessions));
    }

    /**
     * @api
     */
    public function killSession(): JsonResponse
    {
        $hash = $this->request->request->getString('hash');
        $returnAvailable = $this->request->request->getBoolean('returnAvailable');
        $info = $this->sessions->killSession($hash, $returnAvailable);
        if ($info === null) {
            return new JsonResponse([], 204);
        } else {
            return new JsonResponse(SuccessResponse::data($info));
        }
    }
}
