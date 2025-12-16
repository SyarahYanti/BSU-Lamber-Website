@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">📊 Laporan Penjualan</h1>
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- PDF Download -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-file-pdf fa-4x text-danger"></i>
                    </div>
                    <h4 class="card-title text-center mb-4">Download Laporan PDF</h4>
                    
                    <form action="{{ route('penjualan.download-laporan-pdf') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Periode Laporan <span class="text-danger">*</span></label>
                            <select name="periode" class="form-control" id="periode-pdf" required>
                                <option value="">-- Pilih Periode --</option>
                                <option value="bulan">Per Bulan</option>
                                <option value="tahun">Per Tahun</option>
                            </select>
                        </div>

                        <div class="mb-3" id="bulan-pdf-wrapper" style="display: none;">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-control">
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-control" required>
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger btn-lg w-100">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Excel Download -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-file-excel fa-4x text-success"></i>
                    </div>
                    <h4 class="card-title text-center mb-4">Download Laporan Excel</h4>
                    
                    <form action="{{ route('penjualan.download-laporan-excel') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Periode Laporan <span class="text-danger">*</span></label>
                            <select name="periode" class="form-control" id="periode-excel" required>
                                <option value="">-- Pilih Periode --</option>
                                <option value="bulan">Per Bulan</option>
                                <option value="tahun">Per Tahun</option>
                            </select>
                        </div>

                        <div class="mb-3" id="bulan-excel-wrapper" style="display: none;">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-control">
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-control" required>
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-download"></i> Download Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Informasi:</strong>
        <ul class="mb-0 mt-2">
            <li>Laporan <strong>Per Bulan</strong> akan menampilkan data penjualan dalam bulan dan tahun yang dipilih</li>
            <li>Laporan <strong>Per Tahun</strong> akan menampilkan data penjualan selama 1 tahun penuh</li>
            <li>Format <strong>PDF</strong> cocok untuk dicetak atau dibagikan</li>
            <li>Format <strong>Excel</strong> cocok untuk analisis lebih lanjut atau import ke sistem lain</li>
        </ul>
    </div>
</div>

<script>
    // Toggle bulan untuk PDF
    document.getElementById('periode-pdf').addEventListener('change', function() {
        const bulanWrapper = document.getElementById('bulan-pdf-wrapper');
        if (this.value === 'bulan') {
            bulanWrapper.style.display = 'block';
            bulanWrapper.querySelector('select').required = true;
        } else {
            bulanWrapper.style.display = 'none';
            bulanWrapper.querySelector('select').required = false;
        }
    });

    // Toggle bulan untuk Excel
    document.getElementById('periode-excel').addEventListener('change', function() {
        const bulanWrapper = document.getElementById('bulan-excel-wrapper');
        if (this.value === 'bulan') {
            bulanWrapper.style.display = 'block';
            bulanWrapper.querySelector('select').required = true;
        } else {
            bulanWrapper.style.display = 'none';
            bulanWrapper.querySelector('select').required = false;
        }
    });
</script>

<style>
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
    }
    
    .fa-file-pdf, .fa-file-excel {
        margin-bottom: 20px;
    }
</style>
@endsection