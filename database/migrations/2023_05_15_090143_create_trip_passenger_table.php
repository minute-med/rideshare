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
            Schema::create('trip_passenger', function (Blueprint $table) {
                $table->foreignId('trip_id')->constrained('trips');
                $table->foreignId('passenger_id')->constrained('users');
                $table->unsignedTinyInteger('status');
                $table->unsignedTinyInteger('seats');
                $table->index(['passenger_id', 'trip_id']);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::dropIfExists('trip_passenger');
        });
    }
};
