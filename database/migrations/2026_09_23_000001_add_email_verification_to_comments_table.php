<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// SQLite enforces the comments.status CHECK constraint (confirmed: inserting
// an out-of-list value throws "CHECK constraint failed: status"), and SQLite
// has no ALTER TABLE ... MODIFY/DROP CONSTRAINT, so widening the allowed
// values means rebuilding the table -- rename, recreate with the new schema,
// copy the data across, drop the old one. Existing rows keep their current
// status untouched; only new guest comments start out as 'unverified'.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('comments', 'verification_token')) {
            return;
        }

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

    public function down(): void
    {
        if (! Schema::hasColumn('comments', 'verification_token')) {
            return;
        }

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
};
