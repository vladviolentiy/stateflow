<?php

namespace Flow\Notification\Storage\Migrations;

class Migration_0001 extends Migration
{
    public function up(): void
    {
        $this->migrator->query("create table notifications
(
    id        int unsigned auto_increment
        primary key,
    userId    int unsigned                                  not null,
    type      enum ('success', 'danger', 'info', 'warning') not null,
    message   varchar(2048)                                 not null,
    createdAt varchar(64)                                   not null,
    updatedAt varchar(64)                                   not null,
    readAt    varchar(64)                                   null
);

create index notifications_userId_index
    on notifications (userId);

");
    }

    public function related(): array
    {
        return [];
    }
}
