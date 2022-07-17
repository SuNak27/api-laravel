<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AturanPresensi;
use App\Models\DetailGaji;
use App\Models\DetailGajiKaryawan;
// use App\Models\DetailJabatan;
use App\Models\DetailUnit;
use App\Models\Gaji;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\SettingTahun;
use App\Models\Shift;
use App\Models\Unit;
use App\Models\User;
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
        $this->call(UnitSeeder::class);
        $this->call(JabatanSeeder::class);
        $this->call(KaryawanSeeder::class);
        $this->call(DetailJabatanSeeder::class);

        // SettingTahun::create([
        //     'tahun' => '2021',
        //     'status' => '0',
        // ]);

        // SettingTahun::create([
        //     'tahun' => '2022',
        //     'status' => '1',
        // ]);

        // DetailJabatan::create([
        //     'id_karyawan' => 1,
        //     'id_jabatan' => 1,
        //     'status' => '1',
        // ]);
        // DetailJabatan::create([
        //     'id_karyawan' => 2,
        //     'id_jabatan' => 1,
        //     'status' => '1',
        // ]);
        // DetailJabatan::create([
        //     'id_karyawan' => 3,
        //     'id_jabatan' => 1,
        //     'status' => '1',
        // ]);

        // DetailUnit::create([
        //     'id_karyawan' => 1,
        //     'id_unit' => 1,
        //     'status' => '1',
        // ]);
        // DetailUnit::create([
        //     'id_karyawan' => 2,
        //     'id_unit' => 1,
        //     'status' => '1',
        // ]);
        // DetailUnit::create([
        //     'id_karyawan' => 3,
        //     'id_unit' => 1,
        //     'status' => '1',
        // ]);

        // Shift::create([
        //     'id_tahun' => '2',
        //     'kode_shift' => "P-001",
        //     'nama_shift' => "Pagi",
        //     'jam_masuk' => "08:00",
        //     'jam_keluar' => "12:00",
        // ]);

        // Shift::create([
        //     'id_tahun' => '2',
        //     'kode_shift' => "S-001",
        //     'nama_shift' => "Siang",
        //     'jam_masuk' => "13:00",
        //     'jam_keluar' => "17:00",
        // ]);

        // Shift::create([
        //     'id_tahun' => '2',
        //     'kode_shift' => "M-001",
        //     'nama_shift' => "Malam",
        //     'jam_masuk' => "18:00",
        //     'jam_keluar' => "22:00",
        // ]);

        // Jadwal::create([
        //     'id_karyawan' => '1',
        //     'id_tahun' => '2',
        //     'id_jabatan' => '1',
        //     'id_unit' => '1',
        //     'tanggal' => '2022-06-01',
        //     'bulan' => '06',
        // ]);

        // DetailJadwal::create([
        //     'id_jadwal' => '1',
        //     'id_shift' => '1',
        // ]);

        // AturanPresensi::create([
        //     'terlambat' => '15',
        //     'status' => '1',
        //     'denda' => '10000',
        // ]);

        // Gaji::create([
        //     'id_unit' => '1',
        // ]);

        // DetailGaji::create([
        //     'id_gaji' => '1',
        //     'id_jabatan' => '1',
        //     'gaji' => '1500000',
        // ]);

        // DetailGajiKaryawan::create([
        //     'id_detail_gaji' => '1',
        //     'id_karyawan' => '1',
        //     'bulan' => '2022-06',
        //     'denda' => '0',
        // ]);

        // DetailGajiKaryawan::create([
        //     'id_detail_gaji' => '1',
        //     'id_karyawan' => '2',
        //     'bulan' => '2022-06',
        //     'denda' => '0',
        // ]);

        User::create([
            'name' => 'Alfad Sabil Haq',
            'email' => 'admin@ok.com',
            'password' => Hash::make('123456')
        ]);
    }
}
