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
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('trip_id')->constrained('trips');
                $table->foreignId('user_id')->constrained('users');
                $table->string('content');
                $table->unsignedTinyInteger('status');
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
            Schema::dropIfExists('messages');
        });
    }
};
