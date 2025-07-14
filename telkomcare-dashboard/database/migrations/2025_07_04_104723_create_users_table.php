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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Ini akan membuat kolom id BIGINT AUTO_INCREMENT PRIMARY
            $table->string('name');
            $table->string('username')->unique(); // Cukup satu ->unique()
            $table->string('password');
            $table->rememberToken(); // Ini akan membuat kolom remember_token
            $table->timestamps(); // Ini akan membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
