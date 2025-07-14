<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->enum('category', ['K1', 'K2', 'K3']);
            $table->date('entry_date'); // Tanggal data masuk
            $table->integer('sid')->default(0);
            $table->integer('comply')->default(0);
            $table->integer('not_comply')->default(0);
            $table->integer('total')->default(0); // comply + not_comply
            $table->double('target')->default(0); // Persentase
            $table->integer('ttr_comply')->default(0); // Time to Resolution Comply
            $table->double('achievement')->default(0); // Persentase
            $table->integer('ticket_count')->default(0); // Total tiket di entry ini

            $table->timestamps();

            // Pastikan kombinasi city_id, category, dan entry_date unik
            $table->unique(['city_id', 'category', 'entry_date'], 'unique_dashboard_entry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_data');
    }
};