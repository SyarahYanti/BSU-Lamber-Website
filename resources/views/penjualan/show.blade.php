@extends('layouts.app')
@section('title', 'Detail Penjualan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('penjualan.index') }}" class="text-dark">
                <i class="fas fa-arrow-left fa-2x"></i>
            </a>
            <h3 class="text-dark mb-0">Detail Penjualan</h3>
        </div>
        <div>
            <a href="{{ route('penjualan.download-bukti', $penjualan) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download Bukti PDF
            </a>
        </div>
    </div>

    <div class="card shadow" style="max-width: 900px; margin: 0 auto;">
        <div class="card-body p-3">
            <!-- Header Bukti -->
            <div class="text-center mb-3 pb-3 border-bottom">
                <h5 class="fw-bold mb-1">BUKTI PENJUALAN</h5>
                <p class="text-muted mb-0 small">Bank Sampah Unit Lamber</p>
            </div>

            <!-- Info Penjualan -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="140" class="py-1"><strong>ID Penjualan</strong></td>
                            <td class="py-1">: <span class="badge bg-primary">#{{ $penjualan->id }}</span></td>
                        </tr>
                        <tr>
                            <td class="py-1"><strong>Tanggal</strong></td>
                            <td class="py-1">: {{ $penjualan->tanggal_transaksi->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="py-1"><strong>Waktu</strong></td>
                            <td class="py-1">: {{ $penjualan->created_at->format('H:i') }} WITA</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="140" class="py-1"><strong>Nama Nasabah</strong></td>
                            <td class="py-1">: {{ $penjualan->nasabah->nama_nasabah }}</td>
                        </tr>
                        <tr>
                            <td class="py-1"><strong>Alamat</strong></td>
                            <td class="py-1">: {{ $penjualan->nasabah->alamat ?? 'Tidak ada alamat' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1"><strong>Pembayaran</strong></td>
                            <td class="py-1">: 
                                @if($penjualan->tipe_pembayaran == 'tabungan')
                                    <span class="badge bg-info">💰 Simpan ke Tabungan</span>
                                @else
                                    <span class="badge bg-warning">💵 Tunai</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Detail Items -->
            <h6 class="mb-2 fw-bold">Detail Penjualan</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-3">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="40">No</th>
                            <th width="200">Jenis Sampah</th>
                            <th width="100">Berat (kg)</th>
                            <th width="130">Harga/kg</th>
                            <th width="140">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualan->details as $detail)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $detail->jenisSampah->nama ?? 'Jenis tidak ditemukan' }}</td>
                            <td class="text-center">{{ number_format($detail->berat_kg, 2) }}</td>
                            <td class="text-end">Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada detail</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold">TOTAL BERAT:</td>
                            <td class="text-center fw-bold">{{ number_format($penjualan->berat_total, 2) }} kg</td>
                            <td class="text-end fw-bold">TOTAL PENJUALAN:</td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($penjualan->total_jual, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer -->
            <div class="text-center mt-3 pt-3 border-top text-muted">
                <small>Terima kasih telah menjaga lingkungan bersama Bank Sampah Unit Lamber</small>
            </div>
        </div>
    </div>
</div>
@endsection