<?php

namespace Flow\Id\Resources;

use Flow\Core\Interfaces\ResponseInterface;
use Symfony\Component\Uid\UuidV4;

final readonly class RegisterResource implements ResponseInterface
{
    public function __construct(
        private UuidV4 $uuid,
    ) {}

    public function toArray(): array
    {
        return [
            $this->uuid->toRfc4122(),
        ];
    }

    public static function fromState(array $state): static
    {
        return new static(
            UuidV4::fromString($state['uuid']),
        );
    }
}
