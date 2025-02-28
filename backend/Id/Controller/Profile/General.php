<?php

namespace Flow\Id\Controller\Profile;

use Flow\Id\Controller\AuthenticateBaseController;

class General extends AuthenticateBaseController
{
    /**
     * @return array{fNameEncrypted:non-empty-string,lNameEncrypted:non-empty-string,bDayEncrypted:non-empty-string}
     */
    public function getBasicInfo(): array
    {
        $info = $this->storage->getBasicInfo($this->userId);

        return $info;
    }
}
