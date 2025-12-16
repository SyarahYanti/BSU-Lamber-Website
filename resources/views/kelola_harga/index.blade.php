@extends('layouts.app')
@section('title', 'Kelola Harga Sampah')

@section('content')
<div class="min-vh-100 position-relative">
    <div class="position-absolute top-0 start-0 pt-4 ps-5">
        <h2 class="text-dark fw-bold">Kelola Harga Sampah</h2>
    </div>

    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg border-0" style="width: 500px; border-radius: 20px; max-height: 75vh; overflow-y: auto;">
            <div class="card-body p-5">
                <p class="text-muted text-center mb-4">Jenis dan Harga sampah saat ini</p>

                @foreach($jenis as $j)
                <div class="row align-items-center mb-3">
                    <div class="col-5">
                        <span class="fs-5 fw-medium">{{ $j->nama }}</span>
                    </div>
                    <div class="col-1 text-center">
                        <span>=</span>
                    </div>
                    <div class="col-6 text-end">
                        <div class="bg-light border border-success rounded px-3 py-2 fw-bold text-success">
                            Rp. {{ number_format($j->hargaSekarang?->harga_per_kg ?? 0) }}
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="text-center mt-5">
                    <a href="{{ route('kelola_harga.edit') }}" class="btn btn-light border border-2 rounded-pill px-5 py-2 fw-bold shadow-sm">
                        Tambah
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection