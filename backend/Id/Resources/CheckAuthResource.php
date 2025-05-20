<?php

namespace Flow\Id\Resources;

use Flow\Core\Interfaces\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use VladViolentiy\VivaFramework\SuccessResponse;

final readonly class CheckAuthResource implements ResponseInterface
{
    public function __construct(
        private string $ip,
        private string $ua,
        private string $acceptEncoding,
        private string $acceptLang,
    ) {}

    public function toArray(): array
    {
        return [
            'ip' => $this->ip,
            'ua' => $this->ua,
            'accept_encoding' => $this->acceptEncoding,
            'accept_lang' => $this->acceptLang,
        ];
    }

    public function toResponse(): JsonResponse
    {
        return new JsonResponse(SuccessResponse::data($this->toArray()));
    }

    /**
     * @param array{ip: string, ua: string, acceptEncoding: string, acceptLang: string} $state
     * @return static
     */
    public static function fromState(array $state): static
    {
        return new static(
            $state['ip'],
            $state['ua'],
            $state['acceptEncoding'],
            $state['acceptLang'],
        );
    }
}
