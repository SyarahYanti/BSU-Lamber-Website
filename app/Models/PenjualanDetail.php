<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $fillable = ['penjualan_id', 'jenis_sampah_id', 'berat_kg', 'harga_per_kg', 'subtotal'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class);
    }
}