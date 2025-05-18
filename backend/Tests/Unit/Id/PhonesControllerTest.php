<?php

namespace Flow\Tests\Unit\Id;

use Flow\Id\Services\BaseService;
use Flow\Id\Services\Profile\PhonesService;
use Flow\Id\Storage\ArrayStorage\PhoneArrayStorage;
use Flow\Id\Storage\ArrayStorage\UserArrayStorage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VladViolentiy\VivaFramework\Random;

#[CoversClass(PhonesService::class)]
#[CoversClass(UserArrayStorage::class)]
#[CoversClass(BaseService::class)]
class PhonesControllerTest extends TestCase
{
    public function testAddNewPhone(): void
    {
        $controller = new PhonesService(
            new PhoneArrayStorage(),
            1,
        );
        $phone = '375333333333';
        $hash = Random::hash($phone);
        $id = $controller->addNewPhone(
            $phone,
            $hash,
            true,
        );
        $this->assertEquals(1, $id);
    }
}
