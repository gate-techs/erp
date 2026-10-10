<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nursery_id')->constrained('nurseries')->cascadeOnDelete();
            $table->string('cover_image')->nullable();
            $table->decimal('price', 12, 3)->nullable();
            $table->string('discount_type', 20)->default('fixed');
            $table->decimal('discount_value', 12, 3)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nursery_id', 'status', 'sort_order']);
        });

        Schema::create('package_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['package_id', 'locale'], 'package_translation_unique');
        });

        Schema::create('package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['package_id', 'sort_order']);
        });

        Schema::create('package_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_feature_id')
                ->constrained('package_features')
                ->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');

            $table->unique(
                ['package_feature_id', 'locale'],
                'package_feature_translation_unique'
            );
        });

        Schema::create('package_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('image');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['package_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_images');
        Schema::dropIfExists('package_feature_translations');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('package_translations');
        Schema::dropIfExists('packages');
    }
};
