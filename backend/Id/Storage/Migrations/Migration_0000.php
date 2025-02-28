<?php

namespace Flow\Id\Storage\Migrations;

class Migration_0000 extends Migration
{
    public function up(): void
    {
        $this->migrator->query("create table users
(
    id             int unsigned auto_increment
        primary key,
    uuid           binary(16)       not null,
    password       varchar(64)      not null,
    iv             varchar(128)     null,
    salt           varchar(128)     null,
    fNameEncrypted varchar(128)     not null,
    lNameEncrypted varchar(127)     not null,
    bDayEncrypted  varchar(128)     not null,
    isVerificated  bit default b'0' not null,
    globalHash     binary(48)       not null
);");
        $this->migrator->query('create table sessions
(
    id        int unsigned auto_increment
        primary key,
    authHash  binary(48)                           not null,
    userId    int unsigned                         not null,
    createdAt datetime default current_timestamp() not null,
    expiredAt datetime                             not null,
    constraint sessions_users_id_fk
        foreign key (userId) references users (id)
);');
        $this->migrator->query('create index `sessions_authHash(2)_index` on sessions (authHash(4))');
        $this->migrator->query("create table usersEmails
(
    id             int unsigned auto_increment
        primary key,
    userId         int unsigned                         not null,
    emailHash      binary(48)                           not null,
    emailEncrypted text                                 not null,
    allowAuth      bit      default b'0'                not null,
    deleted        bit      default b'0'                not null,
    createdAt      datetime default current_timestamp() not null,
    constraint usersEmails_users_id_fk
        foreign key (userId) references users (id)
);");
        $this->migrator->query("create table usersEncryptInfo
(
    id                  int unsigned auto_increment
        primary key,
    userId              int unsigned                       not null,
    type                enum ('default') default 'default' not null,
    publicKey           text                               null,
    encryptedPrivateKey text                               null,
    constraint usersEncryptInfo_users_id_fk
        foreign key (userId) references users (id)
);");
        $this->migrator->query("create table usersPhones
(
    id             int unsigned auto_increment
        primary key,
    userId         int unsigned                         not null,
    phoneHash      varchar(96)                          not null,
    phoneEncrypted varchar(512)                         not null,
    allowAuth      bit      default b'0'                null,
    createdAt      datetime default current_timestamp() not null,
    constraint usersPhones_users_id_fk
        foreign key (userId) references users (id)
);");

    }

    public function related(): array
    {
        return [];
    }
}
