<?php

namespace Flow\Core;

use Flow\Core\Enums\ServicesEnum;
use mysqli;

class DatabaseConnectionFactory
{
    /** @var array<value-of<ServicesEnum>, mysqli>  */
    private array $connections = [];
    /** @var array<value-of<ServicesEnum>, MysqlAccessor>  */
    private array $mysqliAccessors = [];
    private static ?mysqli $instance = null;

    public static function getInstance(): mysqli
    {
        if (!isset(self::$instance)) {
            self::$instance = Database::createConnection(ServicesEnum::Id);
        }

        return self::$instance;

    }

    public function createConnection(ServicesEnum $database): \mysqli
    {
        if (isset($this->connections[$database->value])) {
            $user = (string) getenv('DB_' . $database->value . '_USER');
            $password = (string) getenv('DB_' . $database->value . '_PASSWORD');
            $db = (string) getenv('DB_' . $database->value . '_DATABASE');
            $server = (string) getenv('DB_' . $database->value . '_SERVER');
            $port = (int) getenv('DB_' . $database->value . '_PORT');

            $this->connections[$database->value] = new mysqli($server, $user, $password, $db, $port);
        }

        return $this->connections[$database->value];
    }

    public function getAccessor(ServicesEnum $database): MysqlAccessor
    {
        if (isset($this->mysqliAccessors[$database->value])) {
            $user = (string) getenv('DB_' . $database->value . '_USER');
            $password = (string) getenv('DB_' . $database->value . '_PASSWORD');
            $db = (string) getenv('DB_' . $database->value . '_DATABASE');
            $server = (string) getenv('DB_' . $database->value . '_SERVER');
            $port = (int) getenv('DB_' . $database->value . '_PORT');

            if (empty($user) || empty($password) || empty($db) || empty($server) || $port > 65535 || $port <= 0) {
                throw new \Exception('Database Connection Failed');
            }

            $this->mysqliAccessors[$database->value] = (new MysqlAccessor())->openSingleConnection(
                $server,
                $user,
                $password,
                $db,
                $port,
            );
        }

        return $this->mysqliAccessors[$database->value];
    }
}
