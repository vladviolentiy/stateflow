<?php

namespace Flow\Id\Resources;

use Flow\Core\Interfaces\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Uid\UuidV4;
use OpenApi\Attributes as OA;
use VladViolentiy\VivaFramework\SuccessResponse;

#[OA\Schema(
    schema: 'RegisterResource',
    properties: [
        new OA\Property(property: 'uuid', description: 'Unique identifier for the registered user', type: 'string', format: 'uuid'),
    ],
    type: 'object',
)]
final readonly class RegisterResource implements ResponseInterface
{
    public function __construct(
        public UuidV4 $uuid,
    ) {}

    public function toArray(): array
    {
        return [
            $this->uuid->toRfc4122(),
        ];
    }

    public function toResponse(): JsonResponse
    {
        return new JsonResponse(SuccessResponse::data([
            'uuid' => $this->uuid->toRfc4122(),
        ]));
    }

    /**
     * @param array{uuid:UuidV4} $state
     * @return static
     */
    public static function fromState(array $state): static
    {
        return new static(
            $state['uuid'],
        );
    }
}
