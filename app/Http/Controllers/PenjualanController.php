<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Tabungan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PenjualanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('nasabah');

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nasabah', function($q) use ($search) {
                $q->where('nama_nasabah', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }

        // Fitur Filter Tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        // Fitur Filter Tipe Pembayaran
        if ($request->filled('tipe_pembayaran')) {
            $query->where('tipe_pembayaran', $request->tipe_pembayaran);
        }

        $penjualans = $query->latest()->paginate(10)->withQueryString();

        // Hitung statistik untuk ditampilkan
        $totalPenjualan = $query->sum('total_jual');
        $totalBerat = $query->sum('berat_total');
        $jumlahTransaksi = $query->count();

        return view('penjualan.index', compact('penjualans', 'totalPenjualan', 'totalBerat', 'jumlahTransaksi'));
    }

    public function create()
    {
        $nasabahs = Nasabah::orderBy('nama_nasabah')->get();
        $jenis = JenisSampah::with('hargaSekarang')->orderBy('nama')->get();
        return view('penjualan.create', compact('nasabahs', 'jenis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'tanggal_transaksi' => 'required|date',
            'berat' => 'required|array',
            'berat.*' => 'numeric|min:0',
            'tipe_pembayaran' => 'required|in:tabungan,tunai',
        ]);

        // Cek apakah ada berat yang diisi
        $adaBerat = false;
        foreach ($request->berat as $berat) {
            if ($berat > 0) {
                $adaBerat = true;
                break;
            }
        }

        if (!$adaBerat) {
            return back()->withErrors(['berat' => 'Minimal harus ada satu jenis sampah yang diisi!'])->withInput();
        }

        $penjualan = Penjualan::create([
            'nasabah_id' => $request->nasabah_id,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'tipe_pembayaran' => $request->tipe_pembayaran,
            'total_jual' => 0,
            'berat_total' => 0,
        ]);

        $totalJual = 0;
        $beratTotal = 0;
        $detailSampah = []; // Untuk tracking jenis sampah

        foreach ($request->berat as $jenisId => $berat) {
            if ($berat > 0) {
                $jenis = JenisSampah::with('hargaSekarang')->find($jenisId);
                
                if (!$jenis) {
                    continue;
                }
                
                $harga = $jenis->harga_per_kg;
                
                if (!$harga || $harga <= 0) {
                    continue;
                }
                
                $subtotal = $berat * $harga;
                $totalJual += $subtotal;
                $beratTotal += $berat;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'jenis_sampah_id' => $jenisId,
                    'berat_kg' => $berat,
                    'harga_per_kg' => $harga,
                    'subtotal' => $subtotal,
                ]);

                // Simpan info jenis sampah untuk tabungan
                $detailSampah[] = $jenis->nama . ' (' . $berat . ' kg)';
            }
        }

        if ($beratTotal == 0) {
            $penjualan->delete();
            return back()->withErrors(['berat' => 'Tidak ada jenis sampah dengan harga yang valid untuk bulan ini!'])->withInput();
        }

        $penjualan->update([
            'total_jual' => $totalJual,
            'berat_total' => $beratTotal,
        ]);

        // ========================================
        // TAMBAHAN BARU: AUTO CREATE TABUNGAN
        // ========================================
        if ($request->tipe_pembayaran === 'tabungan') {
            // Ambil saldo terakhir nasabah
            $saldoSebelumnya = \App\Models\Tabungan::getSaldoTerakhir($request->nasabah_id);
            
            // Hitung saldo baru
            $saldoBaru = $saldoSebelumnya + $totalJual;

            // Buat record tabungan
            \App\Models\Tabungan::create([
                'nasabah_id' => $request->nasabah_id,
                'penjualan_id' => $penjualan->id,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'jenis' => 'setor',
                'jenis_sampah' => implode(', ', $detailSampah), // Gabungkan semua jenis sampah
                'berat_kg' => $beratTotal,
                'debit' => $totalJual, // UANG MASUK
                'kredit' => 0, // TIDAK ADA UANG KELUAR
                'saldo' => $saldoBaru,
                'keterangan' => 'Penjualan ID #' . $penjualan->id,
            ]);
        }
        // ========================================
        // END TAMBAHAN
        // ========================================

        return redirect()->route('penjualan.index')->with('success');
    }
    
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['nasabah', 'details.jenisSampah']);
        return view('penjualan.show', compact('penjualan'));
    }

    public function destroy(Penjualan $penjualan)
    {
        // Hapus tabungan terkait (jika ada)
        if ($penjualan->tipe_pembayaran === 'tabungan') {
            Tabungan::where('penjualan_id', $penjualan->id)->delete();
        }

        // Hapus detail terlebih dahulu
        $penjualan->details()->delete();
        
        // Hapus penjualan
        $penjualan->delete();
        
        return back()->with('success');
    }

    public function downloadBukti(Penjualan $penjualan)
    {
        $penjualan->load(['nasabah', 'details.jenisSampah']);
        
        $pdf = Pdf::loadView('penjualan.bukti', compact('penjualan'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('bukti-penjualan-' . $penjualan->id . '.pdf');
    }

    // FITUR BARU: Laporan
    public function laporan(Request $request)
    {
        return view('penjualan.laporan');
    }

    // FITUR BARU: Download PDF Laporan
    public function downloadLaporanPdf(Request $request)
    {
        $request->validate([
            'periode' => 'required|in:bulan,tahun',
            'bulan' => 'required_if:periode,bulan|nullable|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
        ]);

        $query = Penjualan::with(['nasabah', 'details.jenisSampah']);

        if ($request->periode == 'bulan') {
            $query->whereMonth('tanggal_transaksi', $request->bulan)
                  ->whereYear('tanggal_transaksi', $request->tahun);
            
            // PERBAIKAN
            $judulPeriode = Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F Y');
        } else {
            $query->whereYear('tanggal_transaksi', $request->tahun);
            $judulPeriode = 'Tahun ' . $request->tahun;
        }

        $penjualans = $query->orderBy('tanggal_transaksi', 'desc')->get();

        // Hitung statistik
        $totalPenjualan = $penjualans->sum('total_jual');
        $totalBerat = $penjualans->sum('berat_total');
        $jumlahTransaksi = $penjualans->count();

        // Rekap per jenis sampah
        $rekapJenis = [];
        foreach ($penjualans as $p) {
            foreach ($p->details as $detail) {
                $namaJenis = $detail->jenisSampah->nama ?? 'Tidak diketahui';
                
                if (!isset($rekapJenis[$namaJenis])) {
                    $rekapJenis[$namaJenis] = [
                        'berat' => 0,
                        'total' => 0,
                    ];
                }
                
                $rekapJenis[$namaJenis]['berat'] += $detail->berat_kg;
                $rekapJenis[$namaJenis]['total'] += $detail->subtotal;
            }
        }

        $pdf = Pdf::loadView('penjualan.laporan-pdf', compact(
            'penjualans', 
            'judulPeriode', 
            'totalPenjualan', 
            'totalBerat', 
            'jumlahTransaksi',
            'rekapJenis'
        ));
        
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'laporan-penjualan-' . strtolower(str_replace(' ', '-', $judulPeriode)) . '.pdf';
        return $pdf->download($filename);
    }

    // FITUR BARU: Download Excel Laporan
    public function downloadLaporanExcel(Request $request)
    {
        $request->validate([
            'periode' => 'required|in:bulan,tahun',
            'bulan' => 'required_if:periode,bulan|nullable|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
        ]);

        if ($request->periode == 'bulan') {
            // PERBAIKAN
            $judulPeriode = Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F-Y');
        } else {
            $judulPeriode = 'Tahun-' . $request->tahun;
        }

        $filename = 'laporan-penjualan-' . strtolower($judulPeriode) . '.xlsx';
        
        return Excel::download(
            new PenjualanExport($request->periode, $request->bulan, $request->tahun), 
            $filename
        );
    }
}