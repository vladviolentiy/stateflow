<?php

namespace Flow\Id\DTO;

use Flow\Core\Interfaces\CreateFromRequestInterface;
use Flow\Core\Interfaces\DtoInterface;
use Symfony\Component\HttpFoundation\Request;
use VladViolentiy\VivaFramework\Validation;

readonly class WriteMetaInfoDTO implements CreateFromRequestInterface, DTOInterface
{
    /**
     * @param non-empty-string $token
     * @param non-empty-string $ip
     * @param non-empty-string $userAgent
     * @param non-empty-string $acceptLanguage
     * @param non-empty-string $acceptEncoding
     * @param non-empty-string $lastSeen
     */
    public function __construct(
        public string $token,
        public string $ip,
        public string $userAgent,
        public string $acceptLanguage,
        public string $acceptEncoding,
        public string $lastSeen,
    ) {}

    public static function createFromRequest(Request $request): self
    {
        $session = $request->server->getString('HTTP_AUTHORIZATION');
        $encryptedIp =  $request->request->getString('ip');
        $encryptedUa =  $request->request->getString('ua');
        $encryptedAL =  $request->request->getString('al');
        $encryptedAE =  $request->request->getString('ae');
        $encryptedLastSeen =  $request->request->getString('lastSeen');

        Validation::hash($session);
        Validation::nonEmpty($encryptedIp);
        Validation::nonEmpty($encryptedUa);
        Validation::nonEmpty($encryptedAE);
        Validation::nonEmpty($encryptedAL);
        Validation::nonEmpty($encryptedLastSeen);

        \Flow\Core\Validation::encryptedData($encryptedIp, 'ip');
        \Flow\Core\Validation::encryptedData($encryptedUa, 'ua');
        \Flow\Core\Validation::encryptedData($encryptedAE, 'accept encoding');
        \Flow\Core\Validation::encryptedData($encryptedAL, 'accept language');
        \Flow\Core\Validation::encryptedData($encryptedLastSeen, 'last seen at');

        return new self(
            $session,
            $encryptedIp,
            $encryptedUa,
            $encryptedAL,
            $encryptedAE,
            $encryptedLastSeen,
        );
    }
}
