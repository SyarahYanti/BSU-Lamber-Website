@extends('layouts.app')
@section('title', 'Riwayat Pesanan - ' . $nasabah->nama_nasabah)

@section('content')
<div class="container py-4">
    <!-- Header Kembali + Judul -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ url()->previous() }}" class="me-4 text-black">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h2 class="mb-0 text-black">Riwayat Pesanan</h2>
    </div>

    <!-- Info Nasabah — TANPA CARD, hanya indentasi kiri (persis desainmu) -->
    <div style="margin-left: 56px; margin-bottom: 30px;">
        <div class="mb-2">
            <strong class="text-black">Nama Nasabah</strong> : {{ $nasabah->nama_nasabah }}
        </div>
        <div class="mb-2">
            <strong class="text-black">Nomor Induk</strong> : {{ $nasabah->no_induk ?? '-' }}
        </div>
        <div class="mb-2">
            <strong class="text-black">Alamat</strong> : {{ $nasabah->alamat ?? '-' }}
        </div>
    </div>

    <!-- Search Bulan (opsional nanti bisa difilter) -->
    <div class="mb-4">
        <input type="month" class="form-control w-auto d-inline-block" value="{{ date('Y-m') }}">
    </div>

    <!-- Tabel Riwayat -->
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>Tgl Transaksi</th>
                            <th>Jenis Sampah</th>
                            <th>Jumlah Sampah</th>
                            <th>Berat Total</th>
                            <th>Total Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualans as $penjualan)
                            @foreach($penjualan->details as $detail)
                                <tr>
                                    @if($loop->first)
                                        <td rowspan="{{ $penjualan->details->count() }}">
                                            {{ $penjualan->tanggal_transaksi->format('d-m-Y') }}
                                        </td>
                                    @endif
                                    <td>{{ $detail->jenisSampah->nama }}</td>
                                    <td>{{ $detail->berat_kg }} kg</td>
                                    @if($loop->first)
                                        <td rowspan="{{ $penjualan->details->count() }}">
                                            {{ number_format($penjualan->berat_total, 1) }} kg
                                        </td>
                                        <td rowspan="{{ $penjualan->details->count() }}" class="text-success fw-bold">
                                            Rp {{ number_format($penjualan->total_jual) }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Belum ada riwayat penjualan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $penjualans->links() }}
    </div>
</div>
@endsection