<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PerdinController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SettingPresensiController;
use App\Http\Controllers\SettingTahunController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UnitController;
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

Route::group(['middleware' => ['auth:sanctum']], function () {
});

Route::post('/upload-karyawan', [KaryawanController::class, 'uploadKaryawan']);
Route::post('/upload-jadwal', [JadwalController::class, 'uploadJadwal']);
Route::get('/download-import-karyawan', [KaryawanController::class, 'downloadImportKaryawan']);
Route::get('/download-jabatan', [JabatanController::class, 'downloadJabatan']);
Route::get('/download-unit', [UnitController::class, 'downloadUnit']);
Route::get('/master-data', [MasterDataController::class, 'index']);

Route::get('/admin', [AdminController::class, 'index']);

// Karyawan
Route::resource('/karyawan', KaryawanController::class);
Route::post('/login/karyawan', [KaryawanController::class, 'login']);
Route::get('/karyawanStatistic', [KaryawanController::class, 'statistic']);
Route::get('/karyawanUnit/{id_unit}', [KaryawanController::class, 'karyawanUnit']);

// Jadwal
Route::resource('/jadwal', JadwalController::class);
Route::get('/jadwal/unit/{id_unit}', [JadwalController::class, 'jadwalUnit']);
Route::get('/jadwal/{id_karyawan}/bulan/{bulan}/tahun/{id_tahun}', [JadwalController::class, 'karyawan']);
Route::get('/jadwals', [JadwalController::class, 'bulanTahun']);
Route::get('/jadwal/karyawan/{id_karyawan}/shift/{id_shift}/tanggal/{tanggal}', [JadwalController::class, "check"]);
Route::get("/jadwal/karyawan/{id_karyawan}/tanggal/{tanggal}", [JadwalController::class, "checkShift"]);

// Aturan Presensi
Route::resource('/aturan-presensi', SettingPresensiController::class);

// Presensi
Route::resource('/presensi', PresensiController::class);
Route::get('/presensi/{id_karyawan}/tanggal/{tanggal}', [PresensiController::class, 'id_presensi']);
Route::get('/presensi/karyawan/{id_karyawan}', [PresensiController::class, "karyawan"]);
Route::put('/presensis/{id}', [PresensiController::class, "updateWeb"]);
Route::get('/presensis', [PresensiController::class, "rekap"]);

// Shift
Route::resource('/shift', ShiftController::class);

// Setting Tahun
Route::resource('/setting_tahun', SettingTahunController::class);
Route::get('/setting_tahuns', [SettingTahunController::class, 'tahun_aktif']);

// Jabatan
Route::resource('/jabatan', JabatanController::class);
Route::get('/jabatans/{id_karyawan}', [JabatanController::class, 'detailJabatan']);

// Unit
Route::resource('/unit', UnitController::class);

// Gaji
Route::resource('/gaji', GajiController::class);
Route::get("/gaji/bulan/{bulan}", [GajiController::class, "detailGaji"]);
Route::get("/gajiAll", [GajiController::class, "gajiAll"]);
Route::get("/gaji/check/{id_unit}/{id_jabatan}", [GajiController::class, "checkGaji"]);
Route::post("/gajiKaryawan", [GajiController::class, "gajiKaryawan"]);
// Route::resource('/karyawan', KaryawanController::class);
// Route::resource('/jadwal', JadwalController::class);
Route::post('logout', [AuthController::class, 'logout']);

// SeetingLokasi
Route::resource('/setting_lokasi', LokasiController::class);

// Izin
Route::resource('/izin', IzinController::class);

// Perdin
Route::resource('/perdin', PerdinController::class);

// Lembur
Route::resource('/lembur', LemburController::class);

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    // Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});
