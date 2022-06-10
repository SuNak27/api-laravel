<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SettingPresensiController;
use App\Http\Controllers\SettingTahunController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/admin', [AdminController::class, 'index']);

// Karyawan
Route::resource('/karyawan', KaryawanController::class);
Route::post('/login/karyawan', [KaryawanController::class, 'login']);

// Jadwal
Route::resource('/jadwal', JadwalController::class);
Route::get('/jadwal/{id_karyawan}/bulan/{bulan}/tahun/{id_tahun}', [JadwalController::class, 'karyawan']);
Route::get('/jadwals', [JadwalController::class, 'bulanTahun']);
Route::get('/jadwal/karyawan/{id_karyawan}/shift/{id_shift}/tanggal/{tanggal}', [JadwalController::class, "check"]);

// Aturan Presensi
Route::resource('/aturan-presensi', SettingPresensiController::class);

// Presensi
Route::resource('/presensi', PresensiController::class);
Route::get('/presensi/{id_karyawan}/tanggal/{tanggal}', [PresensiController::class, 'id_presensi']);
Route::get('/presensi/karyawan/{id_karyawan}', [PresensiController::class, "karyawan"]);

// Shift
Route::resource('/shift', ShiftController::class);

// Setting Tahun
Route::resource('/setting_tahun', SettingTahunController::class);

// Jabatan
Route::resource('/jabatan', JabatanController::class);
Route::get('/jabatans/{id_karyawan}', [JabatanController::class, 'detailJabatan']);

// Unit
Route::resource('/unit', UnitController::class);
