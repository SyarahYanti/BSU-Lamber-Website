<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_sampahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_sampah_id')->constrained('jenis_sampahs')->onDelete('cascade');
            $table->integer('harga_per_kg');
            $table->year('tahun');
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->timestamps();

            $table->unique(['jenis_sampah_id', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_sampahs');
    }
};