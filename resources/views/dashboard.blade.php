@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Title & Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #1f5121;">Dashboard</h2>
        
        <!-- Filter Bulan & Tahun -->
        <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
            <select name="bulan" class="form-select form-select-sm" style="width: 150px;">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            
            <select name="tahun" class="form-select form-select-sm" style="width: 120px;">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                        {{ $t }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm" title="Reset ke bulan ini">
                <i class="fas fa-redo"></i>
            </a>
        </form>
    </div>

    <!-- Row 1: Cards Statistik -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Jumlah Nasabah Aktif -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Jumlah Nasabah Aktif</h6>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users fa-lg text-success"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0" style="font-size: 2.5rem; color: #1f5121;">
                        {{ $nasabahAktifSekarang }}
                    </h2>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Pemasukan -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Total Pemasukan</h6>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-money-bill-wave fa-lg text-primary"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0" style="font-size: 1.8rem; color: #0d6efd;">
                        Rp {{ number_format($totalPemasukanBulanIni, 0, ',', '.') }}
                    </h2>
                    <small class="text-muted">Bulan {{ $bulanTerpilih }}</small>
                </div>
            </div>
        </div>

        <!-- Card 3: Jenis Sampah Terbanyak -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Sampah Terbanyak</h6>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-recycle fa-lg text-warning"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1" style="font-size: 1.5rem; color: #ffc107;">
                        {{ $jenisSampahTerbanyak }}
                    </h2>
                    <small class="text-muted">{{ $totalBeratTerbanyak }} kg - {{ $bulanTerpilih }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Tabel & Charts -->
    <div class="row g-3">
        <!-- Tabel Penjualan Terakhir -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-list text-primary"></i> Penjualan Terakhir
                        </h5>
                        <span class="badge bg-primary">{{ $bulanTerpilih }}</span>
                    </div>
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Sampah</th>
                                    <th style="white-space: nowrap;">Berat</th>
                                    <th style="min-width: 110px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($penjualanTerakhir as $p)
                                <tr>
                                    <td>
                                        <strong>{{ $p->nasabah->nama_nasabah }}</strong>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        {{ $p->tanggal_transaksi->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @foreach($p->details->take(2) as $detail)
                                            <span class="badge bg-secondary mb-1">
                                                {{ $detail->jenisSampah->nama }}
                                            </span>
                                        @endforeach
                                        @if($p->details->count() > 2)
                                            <span class="badge bg-light text-dark">+{{ $p->details->count() - 2 }}</span>
                                        @endif
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <strong>{{ number_format($p->berat_total, 1) }} kg</strong>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <strong class="text-success">
                                            Rp {{ number_format($p->total_jual, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Belum ada transaksi di bulan {{ $bulanTerpilih }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
</div>
                </div>
            </div>
        </div>

        <!-- Column untuk 2 Charts -->
        <div class="col-lg-6">
            <!-- Chart Jenis Sampah Masuk -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-bar text-success"></i> Jenis Sampah Masuk
                        </h5>
                        <span class="badge bg-success">{{ $bulanTerpilih }}</span>
                    </div>
                    @if($chartJenisSampah->count() > 0)
                        <div style="position: relative; height: 250px;">
                            <canvas id="chartJenisSampah"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>Belum ada data untuk ditampilkan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chart Statistik Penjualan -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-line text-danger"></i> Statistik Penjualan
                        </h5>
                        <span class="badge bg-danger">Tahun {{ $tahunTerpilih }}</span>
                    </div>
                    <div style="position: relative; height: 250px;">
                        <canvas id="chartStatistikPenjualan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Data dari Backend
    const jenisSampahData = @json($chartJenisSampah);
    const statistikData = @json($statistikPenjualan);
    const tahunTerpilih = @json($tahunTerpilih);

    // Cek apakah ada data untuk chart jenis sampah
    @if($chartJenisSampah->count() > 0)
    // Chart 1: Jenis Sampah Masuk (Bar Chart)
    const ctxJenisSampah = document.getElementById('chartJenisSampah').getContext('2d');
    const chartJenisSampah = new Chart(ctxJenisSampah, {
        type: 'bar',
        data: {
            labels: jenisSampahData.map(item => item.nama),
            datasets: [{
                label: 'Berat (kg)',
                data: jenisSampahData.map(item => parseFloat(item.total_berat)),
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                ],
                borderRadius: 10,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toFixed(2) + ' kg';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' kg';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    @endif

    // Chart 2: Statistik Penjualan (Line Chart)
    const ctxStatistik = document.getElementById('chartStatistikPenjualan').getContext('2d');
    const chartStatistik = new Chart(ctxStatistik, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: tahunTerpilih + ' Report',
                data: statistikData,
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: 'rgb(239, 68, 68)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' transaksi';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Math.floor(value); // Hanya tampilkan integer
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
</script>

<style>
    .card {
        border-radius: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
    }
    .table thead th {
        font-weight: 600;
        font-size: 0.85rem;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
        background-color: #f8f9fa;
    }
    .table tbody tr {
        transition: background-color 0.2s;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        font-weight: 500;
        padding: 0.4em 0.7em;
        font-size: 0.75rem;
    }
    .form-select-sm {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        font-size: 0.875rem;
    }
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endsection