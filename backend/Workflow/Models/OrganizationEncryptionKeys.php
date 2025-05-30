<?php

namespace Flow\Workflow\Models;

use Flow\Core\Interfaces\ModelInterface;
use Flow\Workflow\Enums\EncryptionKeyEnum;

final readonly class OrganizationEncryptionKeys implements ModelInterface
{
    public function __construct(
        public int $id,
        public ?int $organizationId,
        public ?string $encryptedPrivateKey,
        public ?string $publicKey,
        public ?EncryptionKeyEnum $encryptionKey,
        public ?Organization $organization,
    ) {}
}
