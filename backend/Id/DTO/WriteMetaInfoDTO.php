<?php

namespace Flow\Id\DTO;

use Flow\Core\Interfaces\CreateFromRequestInterface;
use Flow\Core\Interfaces\DtoInterface;
use Flow\Id\ValueObject\EncryptedData;
use Symfony\Component\HttpFoundation\Request;
use VladViolentiy\VivaFramework\Validation;

readonly class WriteMetaInfoDTO implements CreateFromRequestInterface, DTOInterface
{
    /**
     * @param non-empty-string $token
     * @param EncryptedData $ip
     * @param EncryptedData $userAgent
     * @param EncryptedData $acceptLanguage
     * @param EncryptedData $acceptEncoding
     * @param EncryptedData $lastSeen
     */
    public function __construct(
        public string $token,
        public EncryptedData $ip,
        public EncryptedData $userAgent,
        public EncryptedData $acceptLanguage,
        public EncryptedData $acceptEncoding,
        public EncryptedData $lastSeen,
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
        return new self(
            $session,
            new EncryptedData($encryptedIp, 'ip'),
            new EncryptedData($encryptedUa, 'ua'),
            new EncryptedData($encryptedAL, 'accept language'),
            new EncryptedData($encryptedAE, 'accept encoding'),
            new EncryptedData($encryptedLastSeen, 'last seen at'),
        );
    }
}
