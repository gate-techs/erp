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
        Schema::create('employee_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // 1. Attendance & Shift Alerts
            $table->boolean('morning_reminder_enabled')->default(true);
            $table->unsignedSmallInteger('morning_reminder_mins')->default(15);
            $table->boolean('checkout_reminder_enabled')->default(true);
            $table->boolean('punch_success_alert')->default(true);

            // 2. Financial & Payroll Alerts
            $table->boolean('payroll_alert_enabled')->default(true);
            $table->boolean('loans_alert_enabled')->default(true);
            $table->boolean('rewards_alert_enabled')->default(true);

            // 3. Requests & Workflow Alerts
            $table->boolean('leave_requests_alert')->default(true);
            $table->boolean('permission_requests_alert')->default(true);

            // 4. Announcements & Admin Alerts
            $table->boolean('admin_memos_alert')->default(true);
            $table->boolean('policy_updates_alert')->default(true);

            // 5. Channels & Quiet Hours
            $table->boolean('push_enabled')->default(true);
            $table->boolean('whatsapp_enabled')->default(false);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('quiet_hours_enabled')->default(false);
            $table->time('quiet_hours_start')->default('20:00:00');
            $table->time('quiet_hours_end')->default('07:00:00');

            $table->timestamps();

            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_notification_preferences');
    }
};
