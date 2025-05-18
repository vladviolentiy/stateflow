<?php

namespace Flow\Id\Services\Profile;

use Flow\Id\DTO\CheckAuthDTO;
use Flow\Id\Resources\CheckAuthResource;
use Flow\Id\Storage\Interfaces\UserStorageInterface;
use VladViolentiy\VivaFramework\Exceptions\NotfoundException;

class GeneralService
{
    /**
     * @param UserStorageInterface $storage
     */
    public function __construct(
        private readonly UserStorageInterface $storage,
    ) {}

    /**
     * @param positive-int $userId
     * @return array{fNameEncrypted:non-empty-string,lNameEncrypted:non-empty-string,bDayEncrypted:non-empty-string}
     * @throws NotfoundException
     */
    public function getBasicInfo(int $userId): array
    {
        $info = $this->storage->getBasicInfo($userId);

        if ($info === null) {
            throw new NotfoundException();
        }

        return $info;
    }

    /**
     * @param array{userId:positive-int,lang:non-empty-string, sessionId: positive-int} $userInfo
     * @param CheckAuthDTO $networkDataDto
     * @return CheckAuthResource
     */
    public function enrichUserInfo(array $userInfo, CheckAuthDTO $networkDataDto): CheckAuthResource
    {
        $userInfo['ip'] = $networkDataDto->ip;
        $userInfo['ua'] = $networkDataDto->ua;
        $userInfo['acceptEncoding'] = $networkDataDto->acceptEncoding;
        $userInfo['acceptLang'] = $networkDataDto->acceptLanguage;

        return CheckAuthResource::fromState($userInfo);
    }
}
