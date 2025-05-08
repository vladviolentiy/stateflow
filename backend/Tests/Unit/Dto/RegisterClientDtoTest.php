<?php

namespace Flow\Tests\Unit\Dto;

use Flow\Id\DTO\RegisterClientDTO;
use Flow\Tests\Helpers\RegisterClientDtoSeeder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegisterClientDTO::class)]
class RegisterClientDtoTest extends TestCase
{
    public function testCreatingWithSeederData(): void
    {
        $req = RegisterClientDtoSeeder::create();

        $this->assertInstanceOf(RegisterClientDto::class, $req);
    }
}
