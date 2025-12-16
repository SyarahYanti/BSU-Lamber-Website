<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabahs';

    protected $fillable = [
        'no_induk',
        'nama_nasabah',
        'alamat',
        'tanggal_daftar',
        'status'
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'status' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    public function scopeTidakAktif($query)
    {
        return $query->where('status', false);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'nasabah_id');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}