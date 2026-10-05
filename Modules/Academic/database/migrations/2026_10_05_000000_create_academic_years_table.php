<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the academic years table.
     *
     * The table is scoped to an educational institution. Branches use the
     * same academic year that belongs to their parent institution.
     */
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            // Primary identifier.
            $table->id();

            // Generic institution owner: nursery, school, institute, etc.
            // It is intentionally not a foreign key yet because the shared
            // institutions table will be introduced in a later migration.
            $table->unsignedBigInteger('institution_id')
                ->comment('Owner institution ID: nursery, school, institute, or training center')
                ->index();

            // Internal unique code, for example: 2026-2027.
            $table->string('code', 30)
                ->comment('Unique academic year code within the institution');

            // Actual calendar boundaries of the academic year.
            $table->date('start_date')
                ->comment('First day of the academic year');
            $table->date('end_date')
                ->comment('Last day of the academic year');

            // Workflow status: draft, active, closed, or archived.
            $table->string('status', 20)
                ->default('draft')
                ->comment('Lifecycle status of the academic year');

            // Convenience flag for quickly identifying the current year.
            $table->boolean('is_current')
                ->default(false)
                ->comment('Whether this is the current academic year for the institution');

            // Optional administrative notes.
            $table->text('notes')
                ->nullable()
                ->comment('Administrative notes about the academic year');

            // User who created the record; user deletion does not delete the year.
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User who created the academic year');

            $table->timestamps();

            // Preserve history safely instead of physically deleting a year.
            $table->softDeletes();

            // The same code cannot be repeated for one institution.
            $table->unique(
                ['institution_id', 'code'],
                'academic_years_institution_code_unique'
            );

            // Common filtering and reporting indexes.
            $table->index(
                ['institution_id', 'status'],
                'academic_years_institution_status_index'
            );
            $table->index(
                ['institution_id', 'start_date', 'end_date'],
                'academic_years_institution_dates_index'
            );
        });
    }

    /**
     * Drop the academic years table.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
