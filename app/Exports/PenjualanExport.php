<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class PenjualanExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithTitle,
    ShouldAutoSize
{
    protected $periode;
    protected $bulan;
    protected $tahun;
    protected $rowNumber = 0;

    public function __construct($periode, $bulan, $tahun)
    {
        $this->periode = $periode;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
     * Return collection of data
     */
    public function collection()
    {
        $query = Penjualan::with(['nasabah', 'details.jenisSampah']);

        if ($this->periode == 'bulan') {
            $query->whereMonth('tanggal_transaksi', $this->bulan)
                  ->whereYear('tanggal_transaksi', $this->tahun);
        } else {
            $query->whereYear('tanggal_transaksi', $this->tahun);
        }

        return $query->orderBy('tanggal_transaksi', 'desc')->get();
    }

    /**
     * Define headers
     */
    public function headings(): array
    {
        return [
            'No',
            'ID Penjualan',
            'Tanggal Transaksi',
            'Nama Nasabah',
            'Jenis Sampah',
            'Berat Total (kg)',
            'Total Jual (Rp)',
            'Tipe Pembayaran',
        ];
    }

    /**
     * Map data to columns
     */
    public function map($penjualan): array
    {
        $this->rowNumber++;

        // Format jenis sampah dengan detail berat
        $details = $penjualan->details->map(function($detail) {
            $namaJenis = $detail->jenisSampah->nama ?? 'Tidak diketahui';
            $berat = number_format($detail->berat_kg, 2, ',', '.');
            return $namaJenis . ' (' . $berat . ' kg)';
        })->join(', ');

        // Jika tidak ada detail
        if (empty($details)) {
            $details = '-';
        }

        return [
            $this->rowNumber,
            $penjualan->id,
            $penjualan->tanggal_transaksi->format('d/m/Y'),
            $penjualan->nasabah->nama_nasabah ?? '-',
            $details,
            number_format($penjualan->berat_total, 2, ',', '.'),
            number_format($penjualan->total_jual, 0, ',', '.'),
            ucfirst($penjualan->tipe_pembayaran),
        ];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header (baris 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2e8b57'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Define sheet title
     */
    public function title(): string
    {
        if ($this->periode == 'bulan') {
            $bulanNama = Carbon::createFromDate($this->tahun, $this->bulan, 1)->translatedFormat('F Y');
            return substr($bulanNama, 0, 31); // Max 31 karakter untuk sheet name
        }
        return 'Tahun ' . $this->tahun;
    }
}