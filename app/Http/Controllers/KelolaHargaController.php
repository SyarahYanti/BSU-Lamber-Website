<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use App\Models\HargaSampah;
use Illuminate\Http\Request;

class KelolaHargaController extends Controller
{
    public function index()
    {
        $jenis = JenisSampah::with(['hargaSekarang' => function($q) {
            $q->where('tahun', now()->year)->where('bulan', now()->month);
        }])->orderBy('nama')->get();

        return view('kelola_harga.index', compact('jenis'));
    }

    public function edit()
    {
        $jenis = JenisSampah::with(['hargaSekarang' => function($q) {
            $q->where('tahun', now()->year)->where('bulan', now()->month);
        }])->orderBy('nama')->get();
        
        return view('kelola_harga.edit', compact('jenis'));
    }

    // UPDATE HARGA DAN NAMA SEMUA JENIS
    public function update(Request $request)
    {
        $request->validate([
            'nama' => 'required|array',
            'nama.*' => 'required|string|max:255',
            'harga' => 'required|array',
            'harga.*' => 'required|integer|min:0',
        ]);

        // Update nama jenis sampah
        foreach ($request->nama as $id => $namaBaru) {
            $jenis = JenisSampah::find($id);
            if ($jenis) {
                // Cek duplikasi nama (kecuali untuk dirinya sendiri)
                $exists = JenisSampah::where('nama', $namaBaru)
                                     ->where('id', '!=', $id)
                                     ->exists();
                
                if ($exists) {
                    return back()->with('error');
                }
                
                $jenis->update(['nama' => ucwords(trim($namaBaru))]);
            }
        }

        // Update harga
        foreach ($request->harga as $id => $hargaBaru) {
            HargaSampah::updateOrCreate(
                [
                    'jenis_sampah_id' => $id,
                    'tahun' => now()->year,
                    'bulan' => now()->month,
                ],
                ['harga_per_kg' => $hargaBaru]
            );
        }

        return redirect()->route('kelola_harga.edit')->with('success');
    }

    // TAMBAH JENIS BARU
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:jenis_sampahs,nama'
        ]);

        JenisSampah::create([
            'nama' => ucwords(trim($request->nama))
        ]);

        return back()->with('success');
    }

    // HAPUS JENIS
    public function destroy(JenisSampah $jenisSampah)
    {
        try {
            $jenisSampah->delete();
            return redirect()->route('kelola_harga.edit')->with('success');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus jenis sampah!');
        }
    }
}