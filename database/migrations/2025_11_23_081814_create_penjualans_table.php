<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabahs')->onDelete('cascade');
            $table->date('tanggal_transaksi');
            $table->bigInteger('total_jual');
            $table->decimal('berat_total', 8, 2);
            $table->string('tipe_pembayaran'); // 'tabungan' atau 'tunai' , untuk hubungan ke tabungan nanti
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};