<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// comments.status has a hard CHECK/ENUM constraint on both environments this
// project actually runs on -- confirmed by testing an insert against it
// before writing anything (SQLite: real CHECK constraint, throws; MySQL: real
// ENUM column type, throws) -- and neither SQLite (no ALTER ... MODIFY/DROP
// CONSTRAINT) nor this table's actual live engine (MySQL 8/MariaDB, ENGINE=
// MyISAM -- confirmed via SHOW CREATE TABLE on live, NOT the SQLite-only
// schema this migration originally assumed applied everywhere) support
// widening an existing CHECK/ENUM in place. Both branches below do the same
// rename/recreate/copy/drop dance, just with driver-correct SQL, and both
// were verified against a table seeded with the *exact* real schema+engine
// (MyISAM, utf8mb4/utf8mb4_unicode_ci) and real row shapes before this file
// was finalized -- the first version of this migration was only ever tested
// against local SQLite and broke live's actual MySQL database on first run.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('comments', 'verification_token')) {
            return;
        }

        match (DB::connection()->getDriverName()) {
            'sqlite' => $this->upSqlite(),
            'mysql' => $this->upMysql(),
            default => throw new \RuntimeException('Unsupported database driver for this migration: ' . DB::connection()->getDriverName()),
        };
    }

    public function down(): void
    {
        if (! Schema::hasColumn('comments', 'verification_token')) {
            return;
        }

        match (DB::connection()->getDriverName()) {
            'sqlite' => $this->downSqlite(),
            'mysql' => $this->downMysql(),
            default => throw new \RuntimeException('Unsupported database driver for this migration: ' . DB::connection()->getDriverName()),
        };
    }

    private function upSqlite(): void
    {
        DB::statement('ALTER TABLE comments RENAME TO comments_old');

        DB::statement("
            CREATE TABLE comments (
                id integer primary key autoincrement not null,
                post_id integer not null,
                user_id integer,
                guest_name varchar,
                guest_email varchar,
                content text not null,
                status varchar check (status in ('unverified', 'pending', 'approved', 'spam')) not null default 'unverified',
                verification_token varchar,
                email_verified_at datetime,
                ip_address varchar,
                created_at datetime,
                updated_at datetime,
                foreign key(post_id) references posts(id) on delete cascade,
                foreign key(user_id) references users(id) on delete set null
            )
        ");

        DB::statement('
            INSERT INTO comments (id, post_id, user_id, guest_name, guest_email, content, status, ip_address, created_at, updated_at)
            SELECT id, post_id, user_id, guest_name, guest_email, content, status, ip_address, created_at, updated_at FROM comments_old
        ');

        DB::statement('DROP TABLE comments_old');

        DB::statement('CREATE INDEX comments_post_id_index on comments (post_id)');
        DB::statement('CREATE INDEX comments_status_index on comments (status)');
        DB::statement('CREATE INDEX comments_created_at_index on comments (created_at)');
        DB::statement('CREATE UNIQUE INDEX comments_verification_token_unique on comments (verification_token)');
    }

    private function downSqlite(): void
    {
        DB::statement('ALTER TABLE comments RENAME TO comments_old');

        DB::statement("
            CREATE TABLE comments (
                id integer primary key autoincrement not null,
                post_id integer not null,
                user_id integer,
                guest_name varchar,
                guest_email varchar,
                content text not null,
                status varchar check (status in ('pending', 'approved', 'spam')) not null default 'pending',
                ip_address varchar,
                created_at datetime,
                updated_at datetime,
                foreign key(post_id) references posts(id) on delete cascade,
                foreign key(user_id) references users(id) on delete set null
            )
        ");

        DB::statement("
            INSERT INTO comments (id, post_id, user_id, guest_name, guest_email, content, status, ip_address, created_at, updated_at)
            SELECT id, post_id, user_id, guest_name, guest_email, content,
                   CASE WHEN status = 'unverified' THEN 'pending' ELSE status END,
                   ip_address, created_at, updated_at FROM comments_old
        ");

        DB::statement('DROP TABLE comments_old');

        DB::statement('CREATE INDEX comments_post_id_index on comments (post_id)');
        DB::statement('CREATE INDEX comments_status_index on comments (status)');
        DB::statement('CREATE INDEX comments_created_at_index on comments (created_at)');
    }

    private function upMysql(): void
    {
        DB::statement('ALTER TABLE comments RENAME TO comments_old');

        DB::statement("
            CREATE TABLE `comments` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `post_id` bigint(20) unsigned NOT NULL,
              `user_id` bigint(20) unsigned DEFAULT NULL,
              `guest_name` varchar(191) DEFAULT NULL,
              `guest_email` varchar(191) DEFAULT NULL,
              `content` text NOT NULL,
              `status` enum('unverified','pending','approved','spam') NOT NULL DEFAULT 'unverified',
              `verification_token` varchar(191) DEFAULT NULL,
              `email_verified_at` timestamp NULL DEFAULT NULL,
              `ip_address` varchar(45) DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT NULL,
              `updated_at` timestamp NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `comments_verification_token_unique` (`verification_token`),
              KEY `comments_user_id_foreign` (`user_id`),
              KEY `comments_post_id_index` (`post_id`),
              KEY `comments_status_index` (`status`),
              KEY `comments_created_at_index` (`created_at`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement('
            INSERT INTO comments (id, post_id, user_id, guest_name, guest_email, content, status, ip_address, created_at, updated_at)
            SELECT id, post_id, user_id, guest_name, guest_email, content, status, ip_address, created_at, updated_at FROM comments_old
        ');

        DB::statement('DROP TABLE comments_old');
    }

    private function downMysql(): void
    {
        DB::statement('ALTER TABLE comments RENAME TO comments_old');

        DB::statement("
            CREATE TABLE `comments` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `post_id` bigint(20) unsigned NOT NULL,
              `user_id` bigint(20) unsigned DEFAULT NULL,
              `guest_name` varchar(191) DEFAULT NULL,
              `guest_email` varchar(191) DEFAULT NULL,
              `content` text NOT NULL,
              `status` enum('pending','approved','spam') NOT NULL DEFAULT 'pending',
              `ip_address` varchar(45) DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT NULL,
              `updated_at` timestamp NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `comments_user_id_foreign` (`user_id`),
              KEY `comments_post_id_index` (`post_id`),
              KEY `comments_status_index` (`status`),
              KEY `comments_created_at_index` (`created_at`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement("
            INSERT INTO comments (id, post_id, user_id, guest_name, guest_email, content, status, ip_address, created_at, updated_at)
            SELECT id, post_id, user_id, guest_name, guest_email, content,
                   CASE WHEN status = 'unverified' THEN 'pending' ELSE status END,
                   ip_address, created_at, updated_at FROM comments_old
        ");

        DB::statement('DROP TABLE comments_old');
    }
};
