<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController as AdminDashboard;
use App\Http\Controllers\MahasiswaController as AdminMahasiswa;
use App\Http\Controllers\DosenController as AdminDosen;
use App\Http\Controllers\ProdiController as AdminProdi;
use App\Http\Controllers\MatkulController as AdminMatkul;
use App\Http\Controllers\KRSController as AdminKRS;
use App\Http\Controllers\FakultasController as AdminFakultas;
use App\Http\Controllers\DashboardController as DosenDashboard;
use App\Http\Controllers\KRSDosenController as DosenKRS;
use App\Http\Controllers\KelasController as AdminKelas;
use App\Http\Controllers\DashboardController as MahasiswaDashboard;
use App\Http\Controllers\KRSController as MahasiswaKRS;
use App\Http\Controllers\KHSController as MahasiswaKHS;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/login', 'welcome');

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif (auth()->user()->isDosen()) {
        return redirect()->route('dosen.dashboard');
    } else {
        return redirect()->route('mahasiswa.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('/profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('mahasiswa', AdminMahasiswa::class);
    Route::resource('/fakultas', AdminFakultas::class);
    Route::resource('/kelas', AdminKelas::class);
    Route::resource('/dosen', AdminDosen::class);
    Route::resource('/prodi', AdminProdi::class);
    Route::resource('/matkul', AdminMatkul::class);
    Route::get('/krs', [AdminKRS::class, 'index'])->name('krs.index');
    Route::post('/krs/{krs}/approve', [AdminKRS::class, 'approve'])->name('krs.approve');
    Route::post('/krs/{krs}/reject', [AdminKRS::class, 'reject'])->name('krs.reject');
});

// Dosen Routes
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenDashboard::class, 'index'])->name('dashboard');
    Route::get('/krs', [DosenKRS::class, 'index'])->name('krs.index');
     Route::get('/krs/{mahasiswa_id}', [DosenKRS::class, 'show'])->name('dosen.krs.show');
    Route::post('/krs/{id}/approve', [DosenKRS::class, 'approveKrs'])->name('krs.approve');
    Route::post('/krs/{id}/reject', [DosenKRS::class, 'rejectKrs'])->name('krs.reject');
});

// Mahasiswa Routes
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboard::class, 'index'])->name('dashboard');
    Route::get('/krs', [MahasiswaKRS::class, 'index'])->name('krs.index');
    Route::post('/krs', [MahasiswaKRS::class, 'store'])->name('krs.store');
    Route::delete('/krs/{krs}', [MahasiswaKRS::class, 'destroy'])->name('krs.destroy');
    Route::resource('/khs', MahasiswaKHS::class);
    Route::get('/khs/{kh}/download', [MahasiswaKHS::class, 'download'])->name('khs.download');
});

require __DIR__ . '/auth.php';