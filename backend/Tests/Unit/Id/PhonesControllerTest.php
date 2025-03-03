<?php

namespace Flow\Tests\Unit\Id;

use Flow\Id\Controller\Profile\PhonesController;
use Flow\Id\Storage\UsersArrayStorage;
use PHPUnit\Framework\TestCase;
use VladViolentiy\VivaFramework\Random;

/**
 * @covers \Flow\Id\Controller\Profile\PhonesController
 * @covers \Flow\Id\Storage\UsersArrayStorage
 * @covers \Flow\Id\Controller\BaseController
 * @covers \Flow\Id\Controller\AuthenticateBaseController
 */
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
