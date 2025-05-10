<?php

namespace Flow\Id\Resources;

use Flow\Core\Interfaces\ResponseInterface;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AuthResource',
    description: 'Authentication result containing session token and encryption params',
    properties: [
        new OA\Property(property: 'hash', description: 'Session token hash', type: 'string'),
        new OA\Property(property: 'salt', description: 'Encryption salt', type: 'string'),
        new OA\Property(property: 'iv', description: 'Initialization vector for encryption', type: 'string'),
    ],
    type: 'object',
)]
final readonly class AuthResource implements ResponseInterface
{
    /**
     * @param non-empty-string $hash
     * @param non-empty-string $salt
     * @param non-empty-string $iv
     */
    public function __construct(
        private string $hash,
        private string $salt,
        private string $iv,
    ) {}

    /**
     * @return array{hash: non-empty-string, salt: non-empty-string, iv: non-empty-string}
     */
    public function toArray(): array
    {
        return [
            'hash' => $this->hash,
            'salt' => $this->salt,
            'iv' => $this->iv,
        ];
    }

    /**
     * @param array{hash: non-empty-string, salt: non-empty-string, iv: non-empty-string} $state
     * @return static
     */
    public static function fromState(array $state): static
    {
        return new static(
            $state['hash'],
            $state['salt'],
            $state['iv'],
        );
    }
}
