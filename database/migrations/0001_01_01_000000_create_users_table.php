<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
		Schema::defaultStringLength(191);
		
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('email')->unique();
            $table->string('password');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('display_name');

            $table->string('role')->default('author');
            $table->string('status')->default('active');

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique();

            $table->text('excerpt')->nullable();
            $table->longText('content');

            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
			$table->id();
			$table->foreignId('post_id')
				->constrained()
				->onDelete('cascade');          // When post is deleted → delete its comments

			$table->foreignId('user_id')
				->nullable()                    // Allow NULL for guest comments
				->constrained()
				->onDelete('set null');         // If user is deleted → keep comment but set user_id to NULL

			// Guest comment fields (only filled when user_id is null)
			$table->string('guest_name')->nullable();
			$table->string('guest_email')->nullable();

			$table->text('content');            // Renamed from 'comment' → clearer name
			$table->enum('status', ['pending', 'approved', 'spam'])->default('pending');

			$table->ipAddress('ip_address')->nullable();  // Track IP for spam/moderation

			$table->timestamps();

			// Performance indexes (very useful for blog with many comments)
			$table->index('post_id');
			$table->index('status');
			$table->index('created_at');
		});

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop in reverse order due to foreign key constraints
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};