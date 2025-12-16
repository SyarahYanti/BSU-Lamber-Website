<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penjualan {{ $judulPeriode }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            font-size: 11px;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2e8b57;
        }
        .logo-img { 
            width: 60px; 
            height: auto; 
            margin-bottom: 8px;
        }
        .title { 
            font-size: 20px; 
            font-weight: bold; 
            color: #2e8b57; 
            margin: 8px 0 5px;
        }
        .subtitle { 
            font-size: 16px; 
            margin: 5px 0; 
            color: #333;
            font-weight: bold;
        }
        .periode {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .statistik {
            margin: 20px 0;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .statistik table {
            width: 100%;
        }
        .statistik td {
            padding: 5px;
            font-size: 12px;
        }
        .statistik .label {
            font-weight: bold;
            width: 200px;
        }
        
        table.data { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
            font-size: 10px;
        }
        table.data th, 
        table.data td { 
            border: 1px solid #333; 
            padding: 6px; 
            text-align: left;
        }
        table.data th { 
            background-color: #2e8b57;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        table.data td.text-center {
            text-align: center;
        }
        table.data td.text-right {
            text-align: right;
        }
        table.data tfoot {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .rekap-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .rekap-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px;
            color: #2e8b57;
            border-bottom: 2px solid #2e8b57;
            padding-bottom: 5px;
        }
        
        table.rekap {
            width: 50%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        table.rekap th,
        table.rekap td {
            border: 1px solid #333;
            padding: 8px;
        }
        table.rekap th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        table.rekap td.text-right {
            text-align: right;
        }
        
        .footer { 
            margin-top: 40px; 
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center; 
            font-size: 9px; 
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="header">
    @php
        $logoPath = public_path('images/logo-BSU.png');
    @endphp
    
    @if(file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="Logo Bank Sampah Lamber" class="logo-img">
    @endif
    
    <div class="title">BANK SAMPAH UNIT LAMBER</div>
    <div class="subtitle">LAPORAN PENJUALAN</div>
    <div class="periode">{{ $judulPeriode }}</div>
</div>

<!-- Statistik -->
<div class="statistik">
    <table>
        <tr>
            <td class="label">Total Transaksi</td>
            <td>: {{ $jumlahTransaksi }} transaksi</td>
            <td class="label">Total Penjualan</td>
            <td>: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Berat</td>
            <td>: {{ number_format($totalBerat, 2, ',', '.') }} kg</td>
            <td class="label">Tanggal Cetak</td>
            <td>: {{ now()->format('d/m/Y H:i') }} WITA</td>
        </tr>
    </table>
</div>

<!-- Tabel Data Penjualan -->
<h3 style="margin: 20px 0 10px; font-size: 13px;">DETAIL TRANSAKSI PENJUALAN</h3>

@if($penjualans->count() > 0)
    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="8%">ID</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Nasabah</th>
                <th width="25%">Jenis Sampah</th>
                <th width="10%">Berat (kg)</th>
                <th width="12%">Total (Rp)</th>
                <th width="8%">Tipe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualans as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">#{{ $p->id }}</td>
                <td class="text-center">{{ $p->tanggal_transaksi->format('d/m/Y') }}</td>
                <td>{{ $p->nasabah->nama_nasabah ?? '-' }}</td>
                <td>
                    @foreach($p->details as $detail)
                        {{ $detail->jenisSampah->nama ?? '-' }} ({{ number_format($detail->berat_kg, 2) }} kg)
                        @if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="text-right">{{ number_format($p->berat_total, 2) }}</td>
                <td class="text-right">{{ number_format($p->total_jual, 0, ',', '.') }}</td>
                <td class="text-center">{{ ucfirst($p->tipe_pembayaran) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($totalBerat, 2) }} kg</td>
                <td class="text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Rekap Per Jenis Sampah -->
    <div class="rekap-section">
        <div class="rekap-title">REKAP PER JENIS SAMPAH</div>
        
        <table class="rekap">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Jenis Sampah</th>
                    <th width="25%">Total Berat (kg)</th>
                    <th width="25%">Total Nilai (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($rekapJenis as $nama => $data)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $nama }}</td>
                    <td class="text-right">{{ number_format($data['berat'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['total'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($totalBerat, 2) }}</td>
                    <td class="text-right">{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="no-data">
        <p>Tidak ada data penjualan untuk periode {{ $judulPeriode }}</p>
    </div>
@endif

<div class="footer">
    <p>Laporan ini dicetak secara otomatis oleh sistem Bank Sampah Unit Lamber</p>
    <p>Dicetak pada: {{ now()->format('d-m-Y H:i') }} WITA</p>
</div>

</body>
</html>