<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DosenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\ProdiController;
use App\Http\Controllers\Api\FakultasController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\MatkulController;
use App\Http\Controllers\Api\JadwalKuliahMhsController;
use App\Http\Controllers\Api\JadwalKuliahController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::apiResource('dosen', DosenController::class);
    Route::apiResource('mahasiswa', MahasiswaController::class);
    Route::apiResource('fakultas', FakultasController::class);
    Route::apiResource('prodi', ProdiController::class);
    Route::apiResource('kelas', KelasController::class);
    Route::apiResource('matkul', MatkulController::class);
    Route::apiResource('jadwal-kuliah', JadwalKuliahController::class);
    Route::apiResource('jadwal-kuliah-mhs', JadwalKuliahMhsController::class);
});
