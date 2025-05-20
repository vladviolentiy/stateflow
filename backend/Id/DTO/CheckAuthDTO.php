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
     * @param non-empty-string $ip
     * @param non-empty-string $ua
     * @param non-empty-string $acceptEncoding
     * @param non-empty-string $acceptLanguage
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
        $ip = $request->server->getString('REMOTE_ADDR');
        $ua = $request->server->getString('HTTP_USER_AGENT');
        $acceptEncoding = $request->server->getString('HTTP_ACCEPT_ENCODING');
        $acceptLanguage = $request->server->getString('HTTP_ACCEPT_LANGUAGE');

        Validation::hash($token);
        Validation::nonEmpty($ip);
        Validation::nonEmpty($ua);
        Validation::nonEmpty($acceptEncoding);
        Validation::nonEmpty($acceptLanguage);

        return new self(
            $token,
            $ip,
            $ua,
            $acceptEncoding,
            $acceptLanguage,
        );
    }
}
