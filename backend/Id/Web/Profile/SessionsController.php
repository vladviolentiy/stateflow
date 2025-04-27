<?php

namespace Flow\Id\Web\Profile;

use Flow\Core\WebPrivate;
use Flow\Id\Services\Profile\SessionsService;
use Flow\Id\Storage\SessionStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VladViolentiy\VivaFramework\SuccessResponse;

class SessionsController extends WebPrivate
{
    private readonly SessionsService $sessions;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->sessions = new SessionsService(new SessionStorage(), $this->info['userId']);
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
        $hash = $this->req->get('hash');
        $returnAvailable = (bool) $this->req->get('returnAvailable');
        $info = $this->sessions->killSession($hash, $returnAvailable);
        if ($info === null) {
            return new JsonResponse([], 204);
        } else {
            return new JsonResponse(SuccessResponse::data($info));
        }
    }
}
