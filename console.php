<?php

require __DIR__ . '/vendor/autoload.php';

use Flow\Core\Database;
use Flow\Core\Enums\ServicesEnum;
use Flow\Id\Storage\Migrations\Migration;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Command\DotenvDumpCommand;
use Symfony\Component\Dotenv\Dotenv;
use VladViolentiy\VivaFramework\Databases\MigrationV2\MysqlMigrationManager;

$dotenv = new Dotenv();
$dotenv->usePutenv();
$dotenv->loadEnv(__DIR__ . '/.env');

$application = new Application();

$application->addCommands([new DotenvDumpCommand(__DIR__, '.env')]);

$application
    ->register('migrate')
    ->setDescription('Take migration on all services')
    ->setCode(function (): void {
        $connection = Database::createConnection(ServicesEnum::Id);
        (new MysqlMigrationManager($connection))->migrate(Migration::$list);
    });

$application->run();
