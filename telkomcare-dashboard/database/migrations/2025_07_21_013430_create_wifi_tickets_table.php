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
        Schema::create('wifi_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('regional');
            $table->decimal('target_percentage', 5, 2);
            $table->enum('status', ['Comply', 'Not Comply']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wifi_tickets');
    }
};