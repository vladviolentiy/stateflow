<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Command\DotenvDumpCommand;

$application = new Application();

$application->addCommands([new DotenvDumpCommand(__DIR__, '.env')]);

$application->run();
