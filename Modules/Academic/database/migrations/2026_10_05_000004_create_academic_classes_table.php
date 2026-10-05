<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the academic classes table.
     */
    public function up(): void
    {
        Schema::create('academic_classes', function (Blueprint $table) {
            // Primary identifier.
            $table->id();

            // Generic institution owner; linked to the shared institution later.
            $table->unsignedBigInteger('institution_id')
                ->comment('Owner institution ID')
                ->index();

            // Generic branch owner; linked to the shared branches later.
            $table->unsignedBigInteger('branch_id')
                ->comment('Branch where the class operates')
                ->index();

            // Academic context of the class.
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete()
                ->comment('Academic year of the class');
            $table->foreignId('academic_stage_id')
                ->constrained('academic_stages')
                ->restrictOnDelete()
                ->comment('Academic stage of the class');

            // Optional maximum number of students allowed in the class.
            $table->unsignedInteger('capacity')
                ->nullable()
                ->comment('Maximum number of students allowed in the class');

            // Lifecycle status: active or inactive.
            $table->string('status', 20)
                ->default('active')
                ->comment('Whether the class is available for use');

            // User who created the class.
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User who created the academic class');

            $table->timestamps();

            // Preserve historical class references.
            $table->softDeletes();

            // Common filtering and tenant-scope index.
            $table->index(
                ['institution_id', 'branch_id', 'academic_year_id', 'academic_stage_id'],
                'academic_classes_scope_index'
            );
            $table->index(
                ['institution_id', 'status'],
                'academic_classes_institution_status_index'
            );
        });
    }

    /**
     * Drop the academic classes table.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_classes');
    }
};
