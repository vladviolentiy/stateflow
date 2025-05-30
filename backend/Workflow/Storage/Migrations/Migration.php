<?php

namespace Flow\Workflow\Storage\Migrations;

use Flow\Notification\Storage\Migrations\Migration_0001;
use VladViolentiy\VivaFramework\Databases\Interfaces\MigrationInterfaceV2;
use VladViolentiy\VivaFramework\Databases\MigrationV2\MigrationsClassInterfaceV2;
use VladViolentiy\VivaFramework\Databases\MysqliV2;

abstract class Migration extends MysqliV2 implements MigrationInterfaceV2
{
    public function __construct(protected readonly MigrationsClassInterfaceV2 $migrator) {}

    /**
     * @var class-string[]
     */
    public static array $list = [
        Migration_0001::class,
    ];
}
