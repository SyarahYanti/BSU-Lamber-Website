<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabahs')->onDelete('cascade');
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans')->onDelete('set null');
            $table->date('tanggal_transaksi');
            $table->enum('jenis', ['setor', 'tarik']); // setor dari penjualan, tarik dari penarikan
            $table->string('jenis_sampah')->nullable(); // nama jenis sampah (untuk transaksi dari penjualan)
            $table->decimal('berat_kg', 8, 2)->nullable(); // berat sampah (untuk transaksi dari penjualan)
            $table->bigInteger('debit')->default(0); // uang masuk
            $table->bigInteger('kredit')->default(0); // uang keluar
            $table->bigInteger('saldo'); // saldo setelah transaksi
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabungans');
    }
};