<?php

namespace Flow\Id\Resources;

use Flow\Core\Interfaces\ResponseInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

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
