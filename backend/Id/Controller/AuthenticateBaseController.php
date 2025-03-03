<?php

namespace Flow\Id\Controller;

use Flow\Id\Storage\StorageInterface;

class AuthenticateBaseController extends BaseController
{
    /**
     * @var positive-int
     */
    protected readonly int $userId;

    /**
     * @param StorageInterface $storage
     * @param positive-int $userId
     */
    public function __construct(StorageInterface $storage, int $userId)
    {
        parent::__construct($storage);
        $this->userId = $userId;
    }
}
