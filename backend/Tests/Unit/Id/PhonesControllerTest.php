<?php

namespace Flow\Tests\Unit\Id;

use Flow\Id\Controller\AuthenticateBaseController;
use Flow\Id\Controller\BaseController;
use Flow\Id\Controller\Profile\PhonesController;
use Flow\Id\Storage\UsersArrayStorage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use VladViolentiy\VivaFramework\Random;

#[CoversClass(PhonesController::class)]
#[CoversClass(UsersArrayStorage::class)]
#[CoversClass(BaseController::class)]
#[CoversClass(AuthenticateBaseController::class)]
class PhonesControllerTest extends TestCase
{
    public function testAddNewPhone(): void
    {
        $controller = new PhonesController(
            new UsersArrayStorage(),
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
