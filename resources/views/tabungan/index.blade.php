@extends('layouts.app')
@section('title', 'Tabungan Nasabah')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tabungan Nasabah</h1>
    </div>

    <!-- Search & Filter Bar -->
    <div class="row g-3 align-items-center mb-4">
        <div class="col-md-3">
            <form action="{{ route('tabungan.index') }}" method="GET" id="searchForm">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari Nama Nasabah" 
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <!-- Hidden inputs untuk filter -->
                <input type="hidden" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                <input type="hidden" name="jenis_sampah" value="{{ request('jenis_sampah') }}">
            </form>
        </div>
        <div class="col-md-7"></div>
        <div class="col-md-2">
            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai', 'jenis', 'jenis_sampah']))
    <div class="mb-3">
        <small class="text-muted">Filter aktif:</small>
        <div class="d-flex flex-wrap gap-2 mt-2">
            @if(request('tanggal_dari'))
                <span class="badge bg-info">Dari: {{ request('tanggal_dari') }}</span>
            @endif
            @if(request('tanggal_sampai'))
                <span class="badge bg-info">Sampai: {{ request('tanggal_sampai') }}</span>
            @endif
            @if(request('jenis'))
                <span class="badge bg-info">Transaksi: {{ ucfirst(request('jenis')) }}</span>
            @endif
            @if(request('jenis_sampah'))
                <span class="badge bg-info">Jenis: {{ request('jenis_sampah') }}</span>
            @endif
            <a href="{{ route('tabungan.index') }}" class="badge bg-danger text-decoration-none">
                <i class="fas fa-times"></i> Hapus Filter
            </a>
        </div>
    </div>
    @endif

    <!-- Tabel Transaksi Tabungan -->
    <div class="card shadow">
        <div class="card-body">
            @if($tabungans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Nasabah</th>
                                <th>Tgl Transaksi</th>
                                <th>Jenis Sampah</th>
                                <th class="text-end">Total Kg</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Kredit</th>
                                <th class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tabungans as $t)
                            <tr>
                                <td>{{ $t->nasabah->nama_nasabah ?? '-' }}</td>
                                <td>{{ $t->tanggal_transaksi->format('d-m-Y') }}</td>
                                <td>
                                    @if($t->jenis_sampah)
                                        <span class="badge bg-secondary">{{ $t->jenis_sampah }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($t->berat_kg)
                                        {{ number_format($t->berat_kg, 1) }} kg
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-success text-end fw-semibold">
                                    @if($t->debit > 0)
                                        Rp {{ number_format($t->debit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-danger text-end fw-semibold">
                                    @if($t->kredit > 0)
                                        Rp {{ number_format($t->kredit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="fw-bold text-end">Rp {{ number_format($t->saldo, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $tabungans->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai', 'jenis', 'jenis_sampah']))
                            Tidak ada transaksi yang sesuai dengan filter
                        @else
                            Belum ada transaksi tabungan
                        @endif
                    </h5>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter"></i> Filter Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tabungan.index') }}" method="GET" id="filterForm">
                <div class="modal-body">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" class="form-control" 
                               value="{{ request('tanggal_dari') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" class="form-control" 
                               value="{{ request('tanggal_sampai') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Transaksi</label>
                        <select name="jenis" class="form-select">
                            <option value="">Semua</option>
                            <option value="setor" {{ request('jenis') == 'setor' ? 'selected' : '' }}>Setor</option>
                            <option value="tarik" {{ request('jenis') == 'tarik' ? 'selected' : '' }}>Tarik</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-50">
                        <i class="fas fa-check"></i> Terapkan
                    </button>
                    <button type="button" class="btn btn-secondary w-50" onclick="resetFilter()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetFilter() {
    // Reset form filter
    document.getElementById('filterForm').reset();
    // Redirect ke halaman tanpa parameter filter
    const searchValue = document.querySelector('input[name="search"]').value;
    if (searchValue) {
        window.location.href = "{{ route('tabungan.index') }}?search=" + searchValue;
    } else {
        window.location.href = "{{ route('tabungan.index') }}";
    }
}
</script>

<style>
.badge {
    font-weight: 500;
    font-size: 0.875rem;
    padding: 0.35rem 0.65rem;
}

.modal-header {
    border-bottom: none;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 1rem;
}

.form-select, .form-control {
    border-radius: 0.375rem;
}

.input-group .form-control-lg {
    border-right: none;
}

.input-group .btn-lg {
    border-left: none;
}
</style>
@endsection