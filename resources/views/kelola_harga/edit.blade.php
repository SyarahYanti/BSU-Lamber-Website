@extends('layouts.app')
@section('title', 'Perbarui Harga Sampah')

@section('content')
<div class="min-vh-100 position-relative">
    <div class="position-absolute top-0 start-0 pt-4 ps-5 d-flex align-items-center gap-3">
        <a href="{{ route('kelola_harga.index') }}" class="text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h2 class="text-dark fw-bold mb-0">Jenis dan Harga Sampah</h2>
    </div>

    <div class="d-flex justify-content-center" style="padding-top: 100px;">
        <div class="card shadow-lg border-0" style="width: 560px; border-radius: 20px;">
            <div class="card-body p-5">

                <p class="text-muted text-center mb-4">Input jenis sampah dan harga di sini</p>

                <!-- FORM TAMBAH JENIS BARU -->
                <div class="text-center mb-5">
                    <form action="{{ route('kelola_harga.store') }}" method="POST" class="d-inline">
                        @csrf
                        <div class="input-group" style="max-width: 420px; margin: 0 auto;">
                            <input type="text" name="nama" class="form-control" placeholder="Tambah jenis sampah baru" required>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        @error('nama')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </form>
                </div>
                
                <!-- FORM UPDATE HARGA DAN NAMA -->
                <form action="{{ route('kelola_harga.update') }}" method="POST" id="formUpdateHarga">
                    @csrf
                    @method('PATCH')

                    <div id="daftarJenis" class="mt-4">
                        @foreach($jenis as $j)
                            @php
                                $hargaSekarang = $j->hargaSekarang?->harga_per_kg ?? 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <!-- INPUT NAMA JENIS (BISA DIEDIT) -->
                                <input type="text" name="nama[{{ $j->id }}]" value="{{ $j->nama }}" 
                                       class="form-control fw-medium" style="width: 180px;" required>
                                
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-bold">=</span>
                                    <input type="number" name="harga[{{ $j->id }}]" value="{{ $hargaSekarang }}"
                                           class="form-control text-end" style="width: 150px;" min="0" required>
                                    
                                    <!-- TOMBOL HAPUS -->
                                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusJenis({{ $j->id }}, '{{ addslashes($j->nama) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- TOMBOL SELESAI -->
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-light border border-2 rounded-pill px-5 py-2 fw-bold shadow-sm">
                            Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FORM HAPUS TERSEMBUNYI -->
<form id="formHapus" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function hapusJenis(id, nama) {
    if (confirm('Yakin hapus ' + nama + '?')) {
        const form = document.getElementById('formHapus');
        form.action = '/kelola-harga/' + id;
        form.submit();
    }
}
</script>

@endsection