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
            Schema::create('vehicle_models', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('vehicle_brand_id')->constrained('vehicle_brands');
                $table->index(['vehicle_brand_id', 'name']);
                $table->timestamps();
            });
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
