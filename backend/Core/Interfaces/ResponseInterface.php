<?php

namespace Flow\Core\Interfaces;

interface ResponseInterface
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;

    /**
     * @param array<mixed> $state
     * @return static
     */
    public static function fromState(array $state): static;
}
