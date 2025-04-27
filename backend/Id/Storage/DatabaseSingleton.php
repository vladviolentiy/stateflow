<?php

namespace Flow\Id\Storage;

use Flow\Core\Database;
use Flow\Core\Enums\ServicesEnum;
use mysqli;

abstract class DatabaseSingleton
{
    private static ?mysqli $instance = null;

    public static function getInstance(): mysqli
    {
        if (!isset(self::$instance)) {
            self::$instance = Database::createConnection(ServicesEnum::Id);
        }

        return self::$instance;

    }
}
