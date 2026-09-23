<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        $now = now();
        DB::table('ai_tasks')->insert([
            ['code' => 'translation', 'name' => 'Translation', 'description' => 'Translate ERP labels and content.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'text_generation', 'name' => 'Text Generation', 'description' => 'Generate or rewrite text.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'report_summary', 'name' => 'Report Summary', 'description' => 'Summarize and explain ERP reports.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ocr_invoice', 'name' => 'Invoice OCR', 'description' => 'Extract data from invoice images or files.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'forecasting', 'name' => 'Forecasting', 'description' => 'Forecast ERP metrics.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tasks');
    }
};
