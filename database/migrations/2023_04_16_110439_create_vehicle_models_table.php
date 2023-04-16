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
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_brand_id')->constrained('vehicle_brands');
            $table->foreignId('vehicle_category_id')->constrained('vehicle_categories');
            $table->string('name');
            $table->integer('year');
            $table->index(['vehicle_category_id', 'name', 'year']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::dropIfExists('vehicle_models');
        });
    }
};
