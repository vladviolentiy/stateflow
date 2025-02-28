<?php

namespace Flow\Id\Storage\Migrations;

use VladViolentiy\VivaFramework\Databases\Interfaces\MigrationInterfaceV2;
use VladViolentiy\VivaFramework\Databases\MigrationV2\MigrationsClassInterfaceV2;
use VladViolentiy\VivaFramework\Databases\Mysqli;

abstract class Migration extends Mysqli implements MigrationInterfaceV2
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
    ];
}
