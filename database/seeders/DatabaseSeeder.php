<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AturanPresensi;
use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\SettingTahun;
use App\Models\Shift;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'nama' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('admin'),
        ]);

        SettingTahun::create([
            'tahun' => '2022',
            'status' => '1',
        ]);

        Jabatan::create([
            'nama_jabatan' => 'Karyawan',
        ]);

        Unit::create([
            'nama_unit' => 'UGD',
        ]);

        Karyawan::create([
            'nama' => "Alfad Sabil Haq",
            'nik' => "123456789",
            'id_jabatan' => 1,
            'id_unit' => 1,
            'tanggal_lahir' => "2020-05-28",
            'status_kawin' => "Belum Kawin",
            'alamat' => "Jl. Raya",
            'gender' => 'L',
            'pendidikan' => 'S1',
            'agama' => 'Islam',
            'telepon' => '081234567890'
        ]);

        Shift::create([
            'id_tahun' => '1',
            'kode_shift' => "P-001",
            'nama_shift' => "Pagi",
            'jam_masuk' => "08:00",
            'jam_keluar' => "12:00",
        ]);

        Shift::create([
            'id_tahun' => '1',
            'kode_shift' => "S-001",
            'nama_shift' => "Siang",
            'jam_masuk' => "13:00",
            'jam_keluar' => "17:00",
        ]);

        Shift::create([
            'id_tahun' => '1',
            'kode_shift' => "M-001",
            'nama_shift' => "Malam",
            'jam_masuk' => "18:00",
            'jam_keluar' => "22:00",
        ]);

        Jadwal::create([
            'id_karyawan' => '1',
            'id_tahun' => '1',
            'tanggal' => '2020-05-28',
        ]);

        AturanPresensi::create([
            'jam_masuk' => '08:00:00',
            'jam_keluar' => '12:00:00',
            'terlambat' => '15',
            'status' => '1',
        ]);
    }
}
