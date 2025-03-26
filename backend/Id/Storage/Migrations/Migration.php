<?php

namespace Flow\Id\Storage\Migrations;

use VladViolentiy\VivaFramework\Databases\Interfaces\MigrationInterfaceV2;
use VladViolentiy\VivaFramework\Databases\MigrationV2\MigrationsClassInterfaceV2;
use VladViolentiy\VivaFramework\Databases\MysqliV2;

abstract class Migration extends MysqliV2 implements MigrationInterfaceV2
{
    protected readonly MigrationsClassInterfaceV2 $migrator;

    public function __construct(MigrationsClassInterfaceV2 $migrator)
    {
        $this->migrator = $migrator;
    }

    /**
     * @var class-string[]
     */
    public static array $list = [
        Migration_0000::class,
        Migration_0001::class,
        Migration_0002::class,
        Migration_0003::class,
        Migration_0004::class,
    ];
}
