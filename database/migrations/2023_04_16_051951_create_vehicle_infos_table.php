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
        Schema::create('vehicle_infos', function (Blueprint $table) {
            $table->id();
        //     $table->string('model_id');
        //     $table->string('color');
        //     $table->string('license_plate');
        //     $table->unsignedSmallInteger('max_seats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::dropIfExists('vehicle_infos');
        });
    }
};
