<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSampah extends Model
{
    protected $table = 'jenis_sampahs';
    protected $fillable = ['nama'];

    public function hargaSekarang()
    {
        return $this->hasOne(HargaSampah::class, 'jenis_sampah_id')
                    ->where('tahun', now()->year)
                    ->where('bulan', now()->month);
    }

    public function semuaHarga()
    {
        return $this->hasMany(HargaSampah::class, 'jenis_sampah_id');
    }

    // TAMBAHKAN INI - Accessor untuk mendapatkan harga
    public function getHargaPerKgAttribute()
    {
        // Coba ambil harga bulan ini
        $harga = $this->hargaSekarang()->first();
        
        // Jika tidak ada, ambil harga terbaru
        if (!$harga) {
            $harga = $this->semuaHarga()->latest('tahun')->latest('bulan')->first();
        }
        
        return $harga ? $harga->harga_per_kg : 0;
    }
}