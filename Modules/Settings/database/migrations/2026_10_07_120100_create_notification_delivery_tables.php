<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel's database notification table. Keep this guarded because an older
        // installation may already have created it outside the module migrations.
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::create('notification_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()
                ->constrained('settings_notification_templates')
                ->nullOnDelete();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('audience_type', 50)->index();
            $table->json('audience_filter')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->unsignedInteger('recipients_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_translations', function (Blueprint $table) {
            $table->id();
            // Deliberately not a foreignUuid: it keeps this migration compatible
            // with existing installations that already own the notifications table.
            $table->uuid('notification_id');
            $table->string('locale', 10);
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->timestamps();
            $table->unique(['notification_id', 'locale'], 'notification_translation_locale_unique');
            $table->index('notification_id');
        });

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('endpoint');
            $table->char('endpoint_hash', 64);
            $table->text('public_key');
            $table->text('auth_token');
            $table->string('content_encoding', 30)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->unique('endpoint_hash', 'push_subscriptions_endpoint_hash_unique');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('notification_translations');
        Schema::dropIfExists('notification_batches');
        // Do not drop notifications here; it may have existed before this module.
    }
};
