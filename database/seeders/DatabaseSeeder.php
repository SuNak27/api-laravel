<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AturanPresensi;
use App\Models\DetailJabatan;
use App\Models\DetailJadwal;
use App\Models\DetailUnit;
use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\SettingTahun;
use App\Models\Shift;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'tahun' => '2021',
            'status' => '0',
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
        Unit::create([
            'nama_unit' => 'Poli Gigi',
        ]);

        DetailJabatan::create([
            'id_karyawan' => 1,
            'id_jabatan' => 1,
            'status' => '1',
        ]);
        DetailJabatan::create([
            'id_karyawan' => 2,
            'id_jabatan' => 1,
            'status' => '1',
        ]);

        DetailUnit::create([
            'id_karyawan' => 1,
            'id_unit' => 1,
            'status' => '0',
            'deleted_at' => now(),
        ]);
        DetailUnit::create([
            'id_karyawan' => 2,
            'id_unit' => 1,
            'status' => '1',
        ]);
        DetailUnit::create([
            'id_karyawan' => 1,
            'id_unit' => 2,
            'status' => '1',
        ]);

        Karyawan::create([
            'nama' => "Alfad Sabil Haq",
            'nik' => "123456789",
            'id_jabatan' => 1,
            'id_unit' => 1,
            'tanggal_lahir' => "2002-03-07",
            'status_kawin' => "Belum Kawin",
            'alamat' => "Paiton",
            'gender' => 'L',
            'pendidikan' => 'S1',
            'agama' => 'Islam',
            'telepon' => '081234567890',
            'username' => 'sabil',
            'password' => Hash::make('123456')
        ]);

        Karyawan::create([
            'nama' => "Ahmad Dani",
            'nik' => "123456789",
            'id_jabatan' => 1,
            'id_unit' => 1,
            'tanggal_lahir' => "2002-02-02",
            'status_kawin' => "Belum Kawin",
            'alamat' => "Jl. Raya",
            'gender' => 'L',
            'pendidikan' => 'S1',
            'agama' => 'Islam',
            'telepon' => '081234567890',
            'username' => 'dani',
            'password' => Hash::make('123456')
        ]);

        Shift::create([
            'id_tahun' => '2',
            'kode_shift' => "P-001",
            'nama_shift' => "Pagi",
            'jam_masuk' => "08:00",
            'jam_keluar' => "12:00",
        ]);

        Shift::create([
            'id_tahun' => '2',
            'kode_shift' => "S-001",
            'nama_shift' => "Siang",
            'jam_masuk' => "13:00",
            'jam_keluar' => "17:00",
        ]);

        Shift::create([
            'id_tahun' => '2',
            'kode_shift' => "M-001",
            'nama_shift' => "Malam",
            'jam_masuk' => "18:00",
            'jam_keluar' => "22:00",
        ]);

        Jadwal::create([
            'id_karyawan' => '1',
            'id_tahun' => '2',
            'id_jabatan' => '1',
            'id_unit' => '1',
            'tanggal' => '2022-06-01',
            'bulan' => '06',
        ]);

        DetailJadwal::create([
            'id_jadwal' => '1',
            'id_shift' => '1',
        ]);

        AturanPresensi::create([
            'jam_masuk' => '08:00:00',
            'jam_keluar' => '12:00:00',
            'terlambat' => '15',
            'status' => '1',
        ]);

        Presensi::create([
            'id_karyawan' => '1',
            'id_shift' => '1',
            'tanggal' => '2022-06-01',
            'jam_masuk' => '08:00:00',
            'jam_keluar' => null,
            'status' => 'Hadir',
            "keterangan" => null,
        ]);
    }
}
