<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('cari');

        $nasabahs = Nasabah::when($search, function($query) use ($search) {
                        $query->where('nama_nasabah', 'like', "%{$search}%")
                              ->orWhere('no_induk', 'like', "%{$search}%");
                    })
                    ->when($request->filled('tanggal_dari'), function($query) use ($request) {
                        $query->whereDate('tanggal_daftar', '>=', $request->tanggal_dari);
                    })
                    ->when($request->filled('tanggal_sampai'), function($query) use ($request) {
                        $query->whereDate('tanggal_daftar', '<=', $request->tanggal_sampai);
                    })
                    ->orderByDesc('id')
                    ->paginate(10);

        return view('nasabah.index', compact('nasabahs'));
    }

    public function create()
    {
        return view('nasabah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_induk' => 'required|unique:nasabahs,no_induk',
            'nama_nasabah' => 'required',
            'alamat' => 'required',
            'tanggal_daftar' => 'required|date',
        ]);

        Nasabah::create($request->all() + ['status' => true]);

        return redirect()->route('nasabah.index')
                         ->with('success');
    }

    public function riwayat(Nasabah $nasabah)
    {
        // Ambil semua penjualan nasabah + detailnya, urutkan dari terbaru
        $penjualans = $nasabah->penjualan()
            ->with('details.jenisSampah')
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('nasabah.riwayat', compact('nasabah', 'penjualans'));
    }

    public function edit(Nasabah $nasabah)
    {
        return view('nasabah.edit', compact('nasabah'));
    }

    public function update(Request $request, Nasabah $nasabah)
    {
        $request->validate([
            'no_induk' => 'required|unique:nasabahs,no_induk,' . $nasabah->id,
            'nama_nasabah' => 'required',
            'alamat' => 'required',
            'tanggal_daftar' => 'required|date',
        ]);

        $nasabah->update($request->all());

        return redirect()->route('nasabah.index')
                         ->with('success');
    }

    public function toggleStatus(Nasabah $nasabah)
    {
        $nasabah->update(['status' => !$nasabah->status]);

        $status = $nasabah->status ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success');
    }

    public function destroy(Nasabah $nasabah)
    {
        $nasabah->delete();
        return back()->with('success');
    }
}