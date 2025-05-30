<?php

namespace Flow\Workflow\Models;

use Flow\Core\Interfaces\ModelInterface;
use Flow\Id\Model\User;

final readonly class OrganizationUsers implements ModelInterface
{
    public function __construct(
        public int $id,
        public ?int $organizationId,
        public ?int $userId,
        public ?string $title,
        public ?Organization $organization,
        public ?User $user,
    ) {}
}
