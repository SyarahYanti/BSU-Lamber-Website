<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Tabungan;
use Illuminate\Http\Request;

class TabunganController extends Controller
{
    // Halaman utama tabungan - Langsung tampilkan transaksi dengan filter nasabah
    public function index(Request $request)
    {
        $query = Tabungan::with('nasabah');

        // Filter berdasarkan nasabah
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nasabah', function($q) use ($search) {
                $q->where('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('no_induk', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        // Filter berdasarkan jenis transaksi
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $tabungans = $query->orderBy('tanggal_transaksi', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20)
                           ->withQueryString();

        // Daftar nasabah untuk dropdown search
        $nasabahs = Nasabah::aktif()->orderBy('nama_nasabah')->get();

        return view('tabungan.index', compact('tabungans', 'nasabahs'));
    }

    // Form tarik tabungan
    public function tarik()
    {
        $nasabahs = Nasabah::aktif()->orderBy('nama_nasabah')->get();
        
        // Hitung saldo untuk setiap nasabah
        foreach ($nasabahs as $nasabah) {
            $nasabah->saldo = Tabungan::getSaldoTerakhir($nasabah->id);
        }

        return view('tabungan.tarik', compact('nasabahs'));
    }

    // Proses tarik tabungan
    public function storeTarik(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $nasabah = Nasabah::findOrFail($request->nasabah_id);
        $saldoSekarang = Tabungan::getSaldoTerakhir($request->nasabah_id);

        // Validasi saldo
        if ($request->jumlah > $saldoSekarang) {
            return back()
                ->withErrors(['jumlah' => 'Jumlah penarikan melebihi saldo tabungan!'])
                ->withInput();
        }

        // Hitung saldo baru
        $saldoBaru = $saldoSekarang - $request->jumlah;

        // Simpan transaksi tarik
        Tabungan::create([
            'nasabah_id' => $request->nasabah_id,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'jenis' => 'tarik',
            'debit' => 0,
            'kredit' => $request->jumlah,
            'saldo' => $saldoBaru,
            'keterangan' => $request->keterangan ?? 'Penarikan Tabungan',
        ]);

        return redirect()
            ->route('tabungan.index')
            ->with('success');
    }

    // API untuk mendapatkan saldo nasabah (untuk AJAX)
    public function getSaldo($nasabah_id)
    {
        $saldo = Tabungan::getSaldoTerakhir($nasabah_id);
        return response()->json(['saldo' => $saldo]);
    }
}