<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\SettingTahun;
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
    }
}
