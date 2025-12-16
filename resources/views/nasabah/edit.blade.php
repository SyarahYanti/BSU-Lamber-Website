@extends('layouts.app')

@section('title', 'Ubah Data Nasabah')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i> Ubah Data Nasabah
                </h4>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('nasabah.update', $nasabah) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Nasabah</label>
                            <input type="text" name="nama_nasabah" class="form-control @error('nama_nasabah') is-invalid @enderror"
                                   value="{{ old('nama_nasabah', $nasabah->nama_nasabah) }}" required autofocus>
                            @error('nama_nasabah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">No. Induk</label>
                            <input type="text" name="no_induk" class="form-control @error('no_induk') is-invalid @enderror"
                                   value="{{ old('no_induk', $nasabah->no_induk) }}" required>
                            @error('no_induk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea name="alamat" rows="4" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $nasabah->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Tanggal Daftar</label>
                        <input type="date" name="tanggal_daftar" class="form-control @error('tanggal_daftar') is-invalid @enderror"
                               value="{{ old('tanggal_daftar', $nasabah->tanggal_daftar->format('Y-m-d')) }}" required>
                        @error('tanggal_daftar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('nasabah.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection