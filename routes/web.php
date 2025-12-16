<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\KelolaHargaController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\AuthController;

// Route Dashboard
Route::get('/', [DashboardController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('nasabah', NasabahController::class);
Route::post('nasabah/{nasabah}/toggle', [NasabahController::class, 'toggleStatus'])->name('nasabah.toggle');
Route::get('nasabah/{nasabah}/riwayat', [NasabahController::class, 'riwayat'])->name('nasabah.riwayat');

Route::prefix('kelola-harga')->name('kelola_harga.')->group(function () {
    Route::get('/', [KelolaHargaController::class, 'index'])->name('index');
    Route::get('/edit', [KelolaHargaController::class, 'edit'])->name('edit');
    Route::patch('/update', [KelolaHargaController::class, 'update'])->name('update');
    Route::post('/store', [KelolaHargaController::class, 'store'])->name('store');
    Route::delete('/{jenisSampah}', [KelolaHargaController::class, 'destroy'])->name('destroy');
});

// ROUTES PENJUALAN
Route::prefix('penjualan')->name('penjualan.')->group(function () {
    Route::get('/', [PenjualanController::class, 'index'])->name('index');
    Route::get('/create', [PenjualanController::class, 'create'])->name('create');
    Route::post('/', [PenjualanController::class, 'store'])->name('store');
    Route::get('/{penjualan}', [PenjualanController::class, 'show'])->name('show');
    Route::delete('/{penjualan}', [PenjualanController::class, 'destroy'])->name('destroy');

    Route::get('/{penjualan}/bukti', [PenjualanController::class, 'downloadBukti'])->name('download-bukti');
    Route::get('/laporan/rekap', [PenjualanController::class, 'laporan'])->name('laporan');
    Route::post('/laporan/download-pdf', [PenjualanController::class, 'downloadLaporanPdf'])->name('download-laporan-pdf');
    Route::post('/laporan/download-excel', [PenjualanController::class, 'downloadLaporanExcel'])->name('download-laporan-excel');
});

// ROUTES TABUNGAN
Route::prefix('tabungan')->name('tabungan.')->group(function () {
    Route::get('/', [TabunganController::class, 'index'])->name('index');
    Route::get('/tarik/form', [TabunganController::class, 'tarik'])->name('tarik');
    Route::post('/tarik/proses', [TabunganController::class, 'storeTarik'])->name('storeTarik');
    Route::get('/api/saldo/{nasabah}', [TabunganController::class, 'getSaldo'])->name('getSaldo');
});

Route::get('/', function () {
    return redirect('/login');
});

// AUTH
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::post('/forgot-password', function (Request $request) {

    $request->validate([
        'email' => 'required|email'
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);

})->name('password.email');


// link yang DIKLIK dari email
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request('email')
    ]);
})->name('password.reset');


// simpan password baru
Route::post('/reset-password', function (Request $request) {

    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password)
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect('/login')->with('status', 'Password berhasil direset')
        : back()->withErrors(['email' => [__($status)]]);
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');










