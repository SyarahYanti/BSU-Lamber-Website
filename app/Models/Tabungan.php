<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nasabah_id',
        'penjualan_id',
        'tanggal_transaksi',
        'jenis',
        'jenis_sampah',
        'berat_kg',
        'debit',
        'kredit',
        'saldo',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'berat_kg' => 'decimal:2',
    ];

    // Relasi ke Nasabah
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    // Relasi ke Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    // Scope untuk filter jenis
    public function scopeSetor($query)
    {
        return $query->where('jenis', 'setor');
    }

    public function scopeTarik($query)
    {
        return $query->where('jenis', 'tarik');
    }

    // Helper untuk mendapatkan saldo terakhir nasabah
    public static function getSaldoTerakhir($nasabah_id)
    {
        $lastTransaction = self::where('nasabah_id', $nasabah_id)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastTransaction ? $lastTransaction->saldo : 0;
    }
}