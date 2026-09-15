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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->default(\App\Enums\UserType::EMPLOYEE->value)->after('id');
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('email')->nullable()->change();
            $table->boolean('is_active')->default(true)->after('password');
            $table->string('avatar')->nullable()->after('is_active');
            $table->string('locale', 5)->default('ar')->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'user_type',
                'username',
                'phone',
                'is_active',
                'avatar',
                'locale',
                'last_login_at',
            ]);
            $table->string('email')->nullable(false)->change();
        });
    }
};
