<?php

namespace Flow\Tests\Iteration;

abstract class DbFunctions
{
    public static function dropTables(\mysqli $connection): bool
    {
        /** @var \mysqli_result $result */
        $result = $connection->query('SHOW TABLES');
        /** @var list<array{0: non-empty-string}> $tablesFromDb */
        $tablesFromDb = $result->fetch_all();
        $tables = array_map(function ($table) {
            return $table[0];
        }, $tablesFromDb);
        if (!empty($tables)) {
            $connection->query('SET FOREIGN_KEY_CHECKS = 0');
            $connection->query('DROP TABLE ' . implode(', ', $tables));
            $connection->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        return true;
    }
}
