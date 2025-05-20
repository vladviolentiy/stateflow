<?php

namespace Flow\Id\Resources;

use Flow\Core\Interfaces\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use VladViolentiy\VivaFramework\SuccessResponse;

final readonly class GetEmailItemResource implements ResponseInterface
{
    public function __construct(
        private string $emailEncrypted,
        private bool $allowAuth,
    ) {}

    public function toArray(): array
    {
        return [
            'emailEncrypted' => $this->emailEncrypted,
            'allowAuth' => $this->allowAuth,
        ];
    }

    /**
     * @param array{emailEncrypted: non-empty-string, allowAuth: int<0,1>} $state
     * @return static
     */
    public static function fromState(array $state): static
    {
        return new static(
            $state['emailEncrypted'],
            (bool) $state['allowAuth'],
        );
    }

    public function toResponse(): JsonResponse
    {
        return new JsonResponse(SuccessResponse::data($this->toArray()));
    }
}
