<?php

namespace Flow\Id\Model;

use Flow\Core\Interfaces\ModelInterface;

final readonly class SessionMeta implements ModelInterface
{
    public function __construct(
        public int $id,
        public ?int $sessionId,
        public ?string $ip,
        public ?string $ua,
        public ?string $acceptLang,
        public ?string $acceptEncoding,
    ) {}
}
