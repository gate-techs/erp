<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //------------------------------------------
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Emergency Contacts (Tab 6 - 4 contacts)
        Schema::create('academic_student_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->tinyInteger('priority')->default(1); // 1, 2, 3, 4
            $table->string('contact_name', 150);
            $table->string('relationship', 100)->nullable();
            $table->string('phone_1', 30);
            $table->string('phone_2', 30)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });

        // Authorized Pickup Persons (Tab 7 - Pick-up policy)
        Schema::create('academic_student_authorized_pickups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('civil_id', 30)->nullable();
            $table->string('relationship', 100)->nullable();
            $table->string('phone', 30);
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    //------------------------------------------
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_student_authorized_pickups');
        Schema::dropIfExists('academic_student_emergency_contacts');
    }
};
