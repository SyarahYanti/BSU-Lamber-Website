<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaSampah extends Model
{
    protected $table = 'harga_sampahs';
    protected $fillable = ['jenis_sampah_id', 'harga_per_kg', 'tahun', 'bulan'];

    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_sampah_id');
    }
}