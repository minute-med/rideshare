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
        Schema::withoutForeignKeyConstraints(function () {
            Schema::create('model_category', function (Blueprint $table) {
                $table->foreignId('vehicle_model_id')->constrained('vehicle_models');
                $table->foreignId('vehicle_category_id')->constrained('vehicle_categories');
                $table->index(['vehicle_model_id', 'vehicle_category_id']);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::dropIfExists('model_category');
        });
    }
};
