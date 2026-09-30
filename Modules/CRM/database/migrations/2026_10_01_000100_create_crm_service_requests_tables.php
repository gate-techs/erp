<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->string('civil_id')->index();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('type')->default('registration_request')->index();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('call_recording_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_service_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')
                ->constrained('crm_service_requests')
                ->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_service_request_attachments');
        Schema::dropIfExists('crm_service_requests');
    }
};
