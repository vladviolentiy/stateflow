<?php

namespace Flow\Id\Storage\Migrations;

class Migration_0004 extends Migration
{
    public function up(): void
    {
        $this->migrator->query('alter table usersPhones modify phoneHash binary(48) not null');
    }

    public function related(): array
    {
        return [
            Migration_0000::class,
        ];
    }
}
