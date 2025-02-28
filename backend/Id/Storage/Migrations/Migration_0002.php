<?php

namespace Flow\Id\Storage\Migrations;

class Migration_0002 extends Migration
{
    public function up(): void
    {
        $this->migrator->query('alter table users modify lNameEncrypted varchar(128) not null');
        $this->migrator->query('alter table usersEncryptInfo add createdAt datetime default now() null');
    }

    public function related(): array
    {
        return [];
    }
}
