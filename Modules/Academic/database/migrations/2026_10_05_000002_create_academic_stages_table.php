<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the academic stages table.
     */
    public function up(): void
    {
        Schema::create('academic_stages', function (Blueprint $table) {
            // Primary identifier.
            $table->id();

            // Generic institution owner: nursery, school, institute, etc.
            // The institutions table will be linked in a later migration.
            $table->unsignedBigInteger('institution_id')
                ->comment('Owner institution ID')
                ->index();

            // Stable internal code, for example: KG1 or PRIMARY.
            $table->string('code', 50)
                ->comment('Unique stage code within the institution');

            // Optional age range in months, mainly used by nurseries.
            $table->unsignedSmallInteger('min_age_months')
                ->nullable()
                ->comment('Minimum accepted age in months');
            $table->unsignedSmallInteger('max_age_months')
                ->nullable()
                ->comment('Maximum accepted age in months');

            // Lifecycle status: active or inactive.
            $table->string('status', 20)
                ->default('active')
                ->comment('Whether the stage is available for use');

            // User who created the stage.
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User who created the academic stage');

            $table->timestamps();

            // Preserve historical references instead of physically deleting stages.
            $table->softDeletes();

            // A stage code is unique within one institution.
            $table->unique(
                ['institution_id', 'code'],
                'academic_stages_institution_code_unique'
            );

            $table->index(
                ['institution_id', 'status'],
                'academic_stages_institution_status_index'
            );
        });
    }

    /**
     * Drop the academic stages table.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_stages');
    }
};
