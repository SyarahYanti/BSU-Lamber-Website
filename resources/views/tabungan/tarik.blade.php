@extends('layouts.app')
@section('title', 'Tarik Tabungan')

@section('content')
<div class="container py-4">
    <!-- Header dengan Panah Kembali -->
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('penjualan.index') }}" class="text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h3 class="mb-0">Tarik Tabungan</h3>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body p-4">
            <form action="{{ route('tabungan.storeTarik') }}" method="POST" id="form-tarik">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Nasabah <span class="text-danger">*</span></label>
                    <select name="nasabah_id" id="nasabah_id" class="form-control" required>
                        <option value="">-- Pilih Nasabah --</option>
                        @foreach($nasabahs as $nasabah)
                            <option value="{{ $nasabah->id }}" 
                                    data-saldo="{{ $nasabah->saldo }}"
                                    {{ old('nasabah_id') == $nasabah->id ? 'selected' : '' }}>
                                {{ $nasabah->nama_nasabah }} (Saldo: Rp {{ number_format($nasabah->saldo, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Saldo Saat Ini</label>
                    <input type="text" id="saldo-display" class="form-control bg-light" readonly value="Rp 0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_transaksi" class="form-control" 
                           value="{{ old('tanggal_transaksi', now()->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Penarikan <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control" 
                           value="{{ old('jumlah') }}" min="1" placeholder="Masukkan jumlah" required>
                    <small class="text-muted">Maksimal sesuai saldo yang tersedia</small>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Proses Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nasabahSelect = document.getElementById('nasabah_id');
    const saldoDisplay = document.getElementById('saldo-display');
    const jumlahInput = document.getElementById('jumlah');

    // Update saldo saat nasabah dipilih
    nasabahSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const saldo = parseFloat(selectedOption.getAttribute('data-saldo')) || 0;
        
        saldoDisplay.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(saldo);
        jumlahInput.max = saldo;
    });

    // Validasi saat input jumlah
    jumlahInput.addEventListener('input', function() {
        const selectedOption = nasabahSelect.options[nasabahSelect.selectedIndex];
        const saldo = parseFloat(selectedOption.getAttribute('data-saldo')) || 0;
        const jumlah = parseFloat(this.value) || 0;

        if (jumlah > saldo) {
            this.setCustomValidity('Jumlah melebihi saldo!');
        } else {
            this.setCustomValidity('');
        }
    });

    // Trigger change jika ada old value
    if (nasabahSelect.value) {
        nasabahSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection