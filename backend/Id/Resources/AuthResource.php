<?php

namespace Flow\Id\Resources;

use Flow\Core\Interfaces\ResponseInterface;

final readonly class AuthResource implements ResponseInterface
{
    public function __construct(
        private string $hash,
        private string $salt,
        private string $iv,
    ) {}

    /**
     * @return array{hash: string, salt: string, iv: string}
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
     * @param array{hash: string, salt: string, iv: string} $state
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
