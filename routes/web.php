<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController as AdminDashboard;
use App\Http\Controllers\MahasiswaController as AdminMahasiswa;
use App\Http\Controllers\DosenController as AdminDosen;
use App\Http\Controllers\MatkulController as AdminMatkul;
use App\Http\Controllers\KRSController as AdminKRS;
use App\Http\Controllers\DashboardController as DosenDashboard;
use App\Http\Controllers\KRSController as DosenKRS;
use App\Http\Controllers\DashboardController as MahasiswaDashboard;
use App\Http\Controllers\KRSController as MahasiswaKRS;

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

Route::view('/', 'welcome');

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif (auth()->user()->isDosen()) {
        return redirect()->route('dosen.dashboard');
    } else {
        return redirect()->route('mahasiswa.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Mahasiswa Management
    Route::resource('/mahasiswa', AdminMahasiswa::class);

    // Dosen Management
    Route::resource('/dosen', AdminDosen::class);

    // Mata Kuliah Management
    Route::resource('/matkul', AdminMatkul::class);

    // KRS Management
    Route::get('/krs', [AdminKRS::class, 'index'])->name('krs.index');
    Route::post('/krs/{krs}/approve', [AdminKRS::class, 'approve'])->name('krs.approve');
    Route::post('/krs/{krs}/reject', [AdminKRS::class, 'reject'])->name('krs.reject');
});

// Dosen Routes
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenDashboard::class, 'index'])->name('dashboard');
    Route::get('/krs', [DosenKRS::class, 'index'])->name('krs.index');
    Route::post('/krs/{krs}/approve', [DosenKRS::class, 'approve'])->name('krs.approve');
    Route::post('/krs/{krs}/reject', [DosenKRS::class, 'reject'])->name('krs.reject');
});

// Mahasiswa Routes
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboard::class, 'index'])->name('dashboard');
    Route::get('/krs', [MahasiswaKRS::class, 'index'])->name('krs.index');
    Route::post('/krs', [MahasiswaKRS::class, 'store'])->name('krs.store');
    Route::delete('/krs/{krs}', [MahasiswaKRS::class, 'destroy'])->name('krs.destroy');
});

require __DIR__ . '/auth.php';
