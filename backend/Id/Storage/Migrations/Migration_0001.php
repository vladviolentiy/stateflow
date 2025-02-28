<?php

namespace Flow\Id\Storage\Migrations;

class Migration_0001 extends Migration
{
    public function up(): void
    {
        $this->migrator->query('alter table users modify iv varchar(64) not null;');
        $this->migrator->query('alter table users modify salt varchar(64) not null;');
        $this->migrator->query("alter table usersPhones modify allowAuth bit default b'0' not null;");
        $this->migrator->query('alter table usersPhones add deleted bit default false not null after allowAuth;');
    }

    public function related(): array
    {
        return [];
    }
}
