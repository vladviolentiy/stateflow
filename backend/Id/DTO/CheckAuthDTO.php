<?php

namespace Flow\Id\DTO;

use Flow\Core\Interfaces\CreateFromRequestInterface;
use Flow\Core\Interfaces\DtoInterface;
use Symfony\Component\HttpFoundation\Request;
use VladViolentiy\VivaFramework\Validation;

readonly class CheckAuthDTO implements CreateFromRequestInterface, DtoInterface
{
    /**
     * @param non-empty-string $token
     * @param string $ip
     * @param string $ua
     * @param string $acceptEncoding
     * @param string $acceptLanguage
     */
    public function __construct(
        public string $token,
        public string $ip,
        public string $ua,
        public string $acceptEncoding,
        public string $acceptLanguage,
    ) {}

    public static function createFromRequest(Request $request): self
    {
        $token = $request->server->getString('HTTP_AUTHORIZATION');
        Validation::hash($token);

        return new self(
            $token,
            $request->server->getString('REMOTE_ADDR'),
            $request->server->getString('HTTP_USER_AGENT'),
            $request->server->getString('HTTP_ACCEPT_ENCODING'),
            $request->server->getString('HTTP_ACCEPT_LANGUAGE'),
        );
    }
}
