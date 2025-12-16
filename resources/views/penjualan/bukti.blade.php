<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Penjualan #{{ $penjualan->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 30px;
            font-size: 12px;
            line-height: 1.6;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2e8b57;
        }
        .logo-img { 
            width: 80px; 
            height: auto; 
            margin-bottom: 10px;
        }
        .title { 
            font-size: 24px; 
            font-weight: bold; 
            color: #2e8b57; 
            margin: 10px 0 5px;
        }
        .subtitle { 
            font-size: 18px; 
            margin: 5px 0; 
            color: #333;
            font-weight: bold;
        }
        .info-section {
            margin: 25px 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        .info-table td:nth-child(2) {
            width: 10px;
        }
        
        table.detail { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 25px 0;
        }
        table.detail th, 
        table.detail td { 
            border: 1px solid #000; 
            padding: 10px; 
            text-align: left;
        }
        table.detail th { 
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        table.detail td.text-center {
            text-align: center;
        }
        table.detail td.text-right {
            text-align: right;
        }
        table.detail tfoot {
            background-color: #f9f9f9;
        }
        table.detail tfoot td {
            font-weight: bold;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 25px 0 15px;
            color: #333;
            border-bottom: 2px solid #2e8b57;
            padding-bottom: 5px;
        }
        
        .total-box { 
            font-size: 18px; 
            font-weight: bold; 
            text-align: right; 
            margin: 30px 0;
            padding: 15px;
            background-color: #f0f0f0;
            border: 2px solid #2e8b57;
        }
        
        .footer { 
            margin-top: 60px; 
            padding-top: 20px;
            border-top: 1px solid #ccc;
            text-align: center; 
            font-size: 10px; 
            color: #666;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #e9ecef;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .badge.tabungan {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .badge.tunai {
            background-color: #fff3cd;
            color: #856404;
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
    <div class="subtitle">BUKTI PENJUALAN</div>
</div>

<div class="info-section">
    <table class="info-table">
        <tr>
            <td>ID Penjualan</td>
            <td>:</td>
            <td>#{{ $penjualan->id }}</td>
        </tr>
        <tr>
            <td>Tanggal Transaksi</td>
            <td>:</td>
            <td>{{ $penjualan->tanggal_transaksi->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>:</td>
            <td>{{ $penjualan->created_at->format('H:i') }} WITA</td>
        </tr>
        <tr>
            <td>Nama Nasabah</td>
            <td>:</td>
            <td>{{ $penjualan->nasabah->nama_nasabah }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $penjualan->nasabah->alamat ?? 'Tidak ada alamat' }}</td>
        </tr>
        <tr>
            <td>Jenis Pembayaran</td>
            <td>:</td>
            <td>
                @if($penjualan->tipe_pembayaran === 'tabungan')
                    <span class="badge tabungan">Simpan ke Tabungan</span>
                @else
                    <span class="badge tunai">Tunai</span>
                @endif
            </td>
        </tr>
    </table>
</div>

<div class="section-title">DETAIL PENJUALAN</div>

<table class="detail">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="40%">Jenis Sampah</th>
            <th width="15%">Berat (kg)</th>
            <th width="20%">Harga/kg</th>
            <th width="20%">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($penjualan->details as $detail)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $detail->jenisSampah->nama ?? 'Tidak ditemukan' }}</td>
            <td class="text-center">{{ number_format($detail->berat_kg, 2, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '. ') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="text-right">TOTAL BERAT:</td>
            <td class="text-center">{{ number_format($penjualan->berat_total, 2, ',', '.') }} kg</td>
            <td class="text-right">TOTAL PENJUALAN:</td>
            <td class="text-right">Rp {{ number_format($penjualan->total_jual, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<div class="total-box">
    TOTAL PENJUALAN: Rp {{ number_format($penjualan->total_jual, 0, ',', '.') }}
</div>

<div class="footer">
    <p>Terima kasih telah menjaga lingkungan bersama Bank Sampah Unit Lamber</p>
    <p>Dicetak pada: {{ now()->format('d-m-Y H:i') }} WITA</p>
    <p style="margin-top: 10px; font-size: 9px;">
        Dokumen ini sah tanpa tanda tangan dan stempel
    </p>
</div>

</body>
</html>