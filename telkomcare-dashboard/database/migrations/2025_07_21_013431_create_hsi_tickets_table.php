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
        Schema::create('hsi_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('treg');
            $table->string('category'); // cth: 4H, 24H
            $table->enum('status', ['Comply', 'Not Comply']);
            $table->decimal('target_percentage', 5, 2);
            $table->date('ticket_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hsi_tickets');
    }
};