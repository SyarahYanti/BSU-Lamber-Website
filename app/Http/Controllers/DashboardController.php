<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\JenisSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan dan tahun dari request, default = bulan/tahun sekarang
        $bulan = $request->filled('bulan') ? $request->bulan : Carbon::now()->month;
        $tahun = $request->filled('tahun') ? $request->tahun : Carbon::now()->year;

        // Buat Carbon instance untuk bulan dan tahun yang dipilih
        $tanggalTerpilih = Carbon::createFromDate($tahun, $bulan, 1);

        // ========================================
        // CARD 1: JUMLAH NASABAH AKTIF
        // ========================================
        $nasabahAktifSekarang = Nasabah::aktif()->count();

        // ========================================
        // CARD 2: TOTAL PEMASUKAN BULAN TERPILIH
        // ========================================
        $totalPemasukanBulanIni = Penjualan::whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->sum('total_jual');

        // ========================================
        // CARD 3: JENIS SAMPAH TERBANYAK BULAN TERPILIH
        // ========================================
        $sampahTerbanyak = PenjualanDetail::join('penjualans', 'penjualan_details.penjualan_id', '=', 'penjualans.id')
            ->join('jenis_sampahs', 'penjualan_details.jenis_sampah_id', '=', 'jenis_sampahs.id')
            ->whereYear('penjualans.tanggal_transaksi', $tahun)
            ->whereMonth('penjualans.tanggal_transaksi', $bulan)
            ->select('jenis_sampahs.nama', DB::raw('SUM(penjualan_details.berat_kg) as total_berat'))
            ->groupBy('jenis_sampahs.id', 'jenis_sampahs.nama')
            ->orderByDesc('total_berat')
            ->first();

        $jenisSampahTerbanyak = $sampahTerbanyak ? $sampahTerbanyak->nama : '-';
        $totalBeratTerbanyak = $sampahTerbanyak ? number_format($sampahTerbanyak->total_berat, 2) : '0';

        // ========================================
        // TABEL: PENJUALAN TERAKHIR (BULAN TERPILIH)
        // ========================================
        $penjualanTerakhir = Penjualan::with(['nasabah', 'details.jenisSampah'])
            ->whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // ========================================
        // CHART 1: JENIS SAMPAH MASUK (BULAN TERPILIH)
        // ========================================
        $chartJenisSampah = PenjualanDetail::join('penjualans', 'penjualan_details.penjualan_id', '=', 'penjualans.id')
            ->join('jenis_sampahs', 'penjualan_details.jenis_sampah_id', '=', 'jenis_sampahs.id')
            ->whereYear('penjualans.tanggal_transaksi', $tahun)
            ->whereMonth('penjualans.tanggal_transaksi', $bulan)
            ->select('jenis_sampahs.nama', DB::raw('SUM(penjualan_details.berat_kg) as total_berat'))
            ->groupBy('jenis_sampahs.id', 'jenis_sampahs.nama')
            ->orderByDesc('total_berat')
            ->limit(5) // Batasi 5 jenis teratas
            ->get();

        // ========================================
        // CHART 2: STATISTIK PENJUALAN PER BULAN (TAHUN TERPILIH)
        // ========================================
        $statistikPenjualan = [];
        for ($i = 1; $i <= 12; $i++) {
            $total = Penjualan::whereYear('tanggal_transaksi', $tahun)
                ->whereMonth('tanggal_transaksi', $i)
                ->count();
            $statistikPenjualan[] = $total;
        }

        // ========================================
        // DATA UNTUK DROPDOWN FILTER
        // ========================================
        $bulanTerpilih = $tanggalTerpilih->translatedFormat('F Y'); // Contoh: Desember 2024
        $tahunTerpilih = $tahun;

        // Daftar tahun untuk dropdown (dari 2020 sampai tahun sekarang + 1)
        $daftarTahun = range(2024, Carbon::now()->year + 1);

        return view('dashboard', compact(
            'nasabahAktifSekarang',
            'totalPemasukanBulanIni',
            'jenisSampahTerbanyak',
            'totalBeratTerbanyak',
            'penjualanTerakhir',
            'chartJenisSampah',
            'statistikPenjualan',
            'bulanTerpilih',
            'tahunTerpilih',
            'bulan',
            'tahun',
            'daftarTahun'
        ));
    }
}