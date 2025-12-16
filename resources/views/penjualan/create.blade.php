@extends('layouts.app')
@section('title', 'Tambah Penjualan Baru')

@section('content')
<div class="container py-4">
    <!-- Header dengan Panah Kembali -->
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('penjualan.index') }}" class="text-dark">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
        <h3 class="mb-0">Tambah Penjualan Baru</h3>
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

    <div class="card shadow" style="max-width: 900px; margin: 0 auto;">
        <div class="card-body p-3">
            <form action="{{ route('penjualan.store') }}" method="POST" id="form-penjualan">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Nasabah <span class="text-danger">*</span></label>
                    <select name="nasabah_id" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Nasabah --</option>
                        @foreach($nasabahs as $nasabah)
                            <option value="{{ $nasabah->id }}" {{ old('nasabah_id') == $nasabah->id ? 'selected' : '' }}>
                                {{ $nasabah->nama_nasabah }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" 
                           value="{{ old('tanggal_transaksi', now()->format('Y-m-d')) }}" required>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <label class="form-label fw-bold">Jenis Sampah</label>
                    @forelse($jenis as $j)
                        @php
                            $harga = $j->harga_per_kg;
                        @endphp
                        <div class="row align-items-center mb-2 sampah-row" data-harga="{{ $harga }}">
                            <div class="col-md-3 col-sm-12 mb-2 mb-md-0">
                                <strong class="small">{{ $j->nama }}</strong>
                                @if($harga <= 0)
                                    <span class="badge bg-warning text-dark ms-1 small">Belum ada harga</span>
                                @endif
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                <div class="input-group input-group-sm">
                                    <button type="button" class="btn btn-outline-secondary btn-minus" {{ $harga <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           name="berat[{{ $j->id }}]" 
                                           class="form-control text-center berat-input" 
                                           value="{{ old('berat.' . $j->id, 0) }}" 
                                           min="0" 
                                           step="0.1"
                                           placeholder="0.0"
                                           {{ $harga <= 0 ? 'readonly' : '' }}>
                                    <button type="button" class="btn btn-outline-secondary btn-plus" {{ $harga <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6 mb-2 mb-md-0 text-muted">
                                <small>Rp <span class="harga-per-kg">{{ number_format($harga, 0, ',', '.') }}</span>/kg</small>
                            </div>
                            <div class="col-md-2 col-sm-6 mb-2 mb-md-0 text-center">
                                <span class="badge bg-secondary berat-kg-display small">0 kg</span>
                            </div>
                            <div class="col-md-2 col-sm-6 text-end">
                                <strong class="subtotal text-success small">Rp 0</strong>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            Belum ada jenis sampah. Silakan tambahkan di menu Kelola Harga terlebih dahulu.
                        </div>
                    @endforelse
                </div>

                <hr class="my-3">

                <div class="bg-light p-3 rounded mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-0">Total Berat: <span id="total-berat" class="text-primary">0</span> kg</h6>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="mb-0 text-success">Total: Rp <span id="grand-total">0</span></h5>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tipe Pembayaran <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipe_pembayaran" 
                               value="tabungan" id="tabungan" 
                               {{ old('tipe_pembayaran', 'tabungan') == 'tabungan' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tabungan">
                            💰 Simpan ke Tabungan
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipe_pembayaran" 
                               value="tunai" id="tunai"
                               {{ old('tipe_pembayaran') == 'tunai' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tunai">
                            💵 Tunai
                        </label>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Buat Penjualan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk format angka ke Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(Math.round(angka));
    }

    // Fungsi utama untuk hitung ulang semua subtotal & total
    function updateTotals() {
        let grandTotal = 0;
        let totalBerat = 0;

        document.querySelectorAll('.sampah-row').forEach((row) => {
            const beratInput = row.querySelector('.berat-input');
            const subtotalEl = row.querySelector('.subtotal');
            const beratDisplay = row.querySelector('.berat-kg-display');
            const harga = parseFloat(row.getAttribute('data-harga')) || 0;

            const berat = parseFloat(beratInput.value) || 0;
            const subtotal = berat * harga;

            // Update tampilan berat
            if (beratDisplay) {
                beratDisplay.textContent = berat.toFixed(1) + ' kg';
            }
            
            // Update subtotal di baris ini
            if (subtotalEl) {
                subtotalEl.textContent = 'Rp ' + formatRupiah(subtotal);
            }
            
            // Tambah ke total keseluruhan
            grandTotal += subtotal;
            totalBerat += berat;
        });

        // Update total di bawah
        const grandTotalEl = document.getElementById('grand-total');
        const totalBeratEl = document.getElementById('total-berat');
        
        if (grandTotalEl) {
            grandTotalEl.textContent = formatRupiah(grandTotal);
        }
        
        if (totalBeratEl) {
            totalBeratEl.textContent = totalBerat.toFixed(2);
        }
    }

    // Event: saat halaman selesai load
    document.addEventListener('DOMContentLoaded', function () {
        // Pasang event ke semua tombol +
        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (this.disabled) return;
                
                const input = this.previousElementSibling;
                if (input && input.classList.contains('berat-input')) {
                    const currentValue = parseFloat(input.value) || 0;
                    input.value = (currentValue + 0.1).toFixed(1);
                    updateTotals();
                }
            });
        });

        // Pasang event ke semua tombol -
        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (this.disabled) return;
                
                const input = this.nextElementSibling;
                if (input && input.classList.contains('berat-input')) {
                    const currentValue = parseFloat(input.value) || 0;
                    if (currentValue > 0) {
                        input.value = Math.max(0, currentValue - 0.1).toFixed(1);
                        updateTotals();
                    }
                }
            });
        });

        // Pasang event kalau user ketik manual di input berat
        document.querySelectorAll('.berat-input').forEach(input => {
            input.addEventListener('input', function() {
                // Pastikan nilai tidak negatif
                if (parseFloat(this.value) < 0) {
                    this.value = 0;
                }
                updateTotals();
            });
            
            input.addEventListener('change', function() {
                // Format ulang saat blur
                const value = parseFloat(this.value) || 0;
                this.value = value.toFixed(1);
                updateTotals();
            });
        });

        // Hitung pertama kali saat halaman dibuka
        updateTotals();
    });
</script>

<style>
    .sampah-row {
        padding: 8px;
        border-radius: 5px;
        transition: background-color 0.2s;
    }
    
    .sampah-row:hover {
        background-color: #f8f9fa;
    }
    
    .berat-input {
        font-weight: bold;
        max-width: 70px;
    }
    
    .btn-minus, .btn-plus {
        padding: 0.2rem 0.4rem;
    }
</style>
@endsection