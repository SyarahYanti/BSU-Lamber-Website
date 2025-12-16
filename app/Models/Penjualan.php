<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nasabah_id',
        'tanggal_transaksi',
        'tipe_pembayaran',
        'total_jual',
        'berat_total',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'total_jual' => 'decimal:2',
        'berat_total' => 'decimal:2',
    ];

    // Relasi ke Nasabah
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    // Relasi ke Detail Penjualan
    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    // Relasi ke Tabungan
    public function tabungan()
    {
        return $this->hasOne(Tabungan::class);
    }
}