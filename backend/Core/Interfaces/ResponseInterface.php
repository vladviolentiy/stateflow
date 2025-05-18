<?php

namespace Flow\Core\Interfaces;

use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function toResponse(): JsonResponse;
}
