@extends('layouts.app')
@section('title', 'Daftar Penjualan')

@section('content')
<div class="container py-4">
    <!-- Header dengan Pencarian dan Download Laporan -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Penjualan</h1>
        
        <div class="d-flex gap-2">
            <!-- Form Pencarian -->
            <form action="{{ route('penjualan.index') }}" method="GET" class="d-flex">
                <input type="hidden" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                <input type="hidden" name="tipe_pembayaran" value="{{ request('tipe_pembayaran') }}">
                
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari ID atau Nama Nasabah" 
                       value="{{ request('search') }}"
                       style="width: 250px;">
                <button type="submit" class="btn btn-primary ms-2">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <!-- Tombol Download Laporan -->
            <a href="{{ route('penjualan.laporan') }}" class="btn btn-info">
                <i class="fas fa-file-alt"></i> Download Rekap Penjualan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tombol Tambah dan Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Tombol Tambah Penjualan (Kiri) -->
        <div class="d-flex gap-2">
            <a href="{{ route('penjualan.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Penjualan
            </a>
            <a href="{{ route('tabungan.tarik') }}" class="btn btn-success">
                <i class="fas fa-hand-holding-usd"></i> Tarik Tabungan
            </a>
        </div>

        <!-- Tombol Filter (Kanan) -->
        <div class="position-relative">
            <button type="button" class="btn btn-primary" id="toggleFilter">
                <i class="fas fa-filter"></i> Filter
                @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai', 'tipe_pembayaran']))
                    <span class="badge bg-danger rounded-pill">●</span>
                @endif
            </button>

            <!-- Card Filter (Hidden by default) -->
            <div id="filterCard" class="card shadow-lg position-absolute end-0 mt-2" style="display: none; width: 400px; z-index: 1000;">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-filter"></i> Filter Data</span>
                    <button type="button" class="btn-close btn-close-white" id="closeFilter"></button>
                </div>
                <div class="card-body">
                    <form action="{{ route('penjualan.index') }}" method="GET" id="filterForm">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="tanggal_dari" class="form-control" 
                                   value="{{ request('tanggal_dari') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="tanggal_sampai" class="form-control" 
                                   value="{{ request('tanggal_sampai') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipe Pembayaran</label>
                            <select name="tipe_pembayaran" class="form-control">
                                <option value="">Semua</option>
                                <option value="tabungan" {{ request('tipe_pembayaran') == 'tabungan' ? 'selected' : '' }}>Tabungan</option>
                                <option value="tunai" {{ request('tipe_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-check"></i> Terapkan
                            </button>
                            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleFilter');
            const filterCard = document.getElementById('filterCard');
            const closeBtn = document.getElementById('closeFilter');

            // Toggle filter card
            toggleBtn.addEventListener('click', function() {
                filterCard.style.display = filterCard.style.display === 'none' ? 'block' : 'none';
            });

            // Close filter card
            closeBtn.addEventListener('click', function() {
                filterCard.style.display = 'none';
            });

            // Close when clicking outside
            document.addEventListener('click', function(event) {
                if (!toggleBtn.contains(event.target) && !filterCard.contains(event.target)) {
                    filterCard.style.display = 'none';
                }
            });
        });
    </script>

    <!-- Statistik -->
    @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai', 'tipe_pembayaran']))
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Transaksi</h6>
                    <h3>{{ $jumlahTransaksi }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Total Penjualan</h6>
                    <h3>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Total Berat</h6>
                    <h3>{{ number_format($totalBerat, 2) }} kg</h3>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabel Data -->
    <div class="card shadow">
        <div class="card-body">
            @if($penjualans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Nasabah</th>
                                <th>Tgl Transaksi</th>
                                <th>Total Jual</th>
                                <th>Berat Total</th>
                                <th>Tipe</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penjualans as $p)
                            <tr>
                                <td><span class="badge bg-primary">#{{ $p->id }}</span></td>
                                <td>{{ $p->nasabah->nama_nasabah ?? '-' }}</td>
                                <td>
                                    @if($p->tanggal_transaksi instanceof \Carbon\Carbon)
                                        {{ $p->tanggal_transaksi->format('d/m/Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($p->tanggal_transaksi)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td class="fw-bold text-success">Rp {{ number_format($p->total_jual, 0, ',', '.') }}</td>
                                <td>{{ number_format($p->berat_total, 2) }} kg</td>
                                <td>
                                    @if($p->tipe_pembayaran == 'tabungan')
                                        <span class="badge bg-info">💰 Tabungan</span>
                                    @else
                                        <span class="badge bg-warning">💵 Tunai</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('penjualan.show', $p) }}" 
                                           class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('penjualan.destroy', $p) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus penjualan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $penjualans->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai', 'tipe_pembayaran']))
                            Tidak ada data yang sesuai dengan filter
                        @else
                            Belum ada data penjualan
                        @endif
                    </h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai', 'tipe_pembayaran']))
                            Coba ubah filter pencarian Anda
                        @else
                            Klik tombol "Tambah Penjualan" untuk membuat penjualan baru
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection