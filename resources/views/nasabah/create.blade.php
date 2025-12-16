@extends('layouts.app')

@section('content')
<div class="container-fluid ">
    <h1 class="h3 mb-4 text-gray-800">Tambah Nasabah</h1>

    <div class="card shadow col-lg-7">
        <div class="card-body p-4">
            <form action="{{ route('nasabah.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Nasabah</label>
                            <input type="text" name="nama_nasabah" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>No. Induk</label>
                            <input type="text" name="no_induk" class="form-control" required>

                            @error('no_induk')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3 col-md-6">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3 col-md-6">
                    <label>Tanggal Daftar</label>
                    <input type="date" name="tanggal_daftar" class="form-control" value="{{ old('tanggal_daftar', now()->format('Y-m-d')) }}" required>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('nasabah.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection