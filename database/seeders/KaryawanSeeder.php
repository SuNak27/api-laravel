<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Karyawan::create([
            'nik_karyawan' => "123456789",
            'nama_karyawan' => "Alfad Sabil Haq",
            'tanggal_lahir' => "2002-03-07",
            'status_kawin' => "Belum Kawin",
            'alamat' => "Perumahan De Tanjung Raya",
            'gender' => 'L',
            'pendidikan' => 'S1',
            'agama' => 'Islam',
            'telepon' => '081234567890',
            'username' => 'sabil',
            'password' => Hash::make('123456')
        ]);

        Karyawan::create([
            'nik_karyawan' => "351233332122",
            'nama_karyawan' => "Ahmad Dani",
            'tanggal_lahir' => "2002-04-27",
            'status_kawin' => "Belum Kawin",
            'alamat' => "Bondowoso",
            'gender' => 'L',
            'pendidikan' => 'S1',
            'agama' => 'Islam',
            'telepon' => '088231231333',
            'username' => 'dani',
            'password' => Hash::make('123456')
        ]);

        Karyawan::create([
            'nik_karyawan' => "31233332122",
            'nama_karyawan' => "Sinta Nurfaeda",
            'tanggal_lahir' => "2002-06-03",
            'status_kawin' => "Belum Kawin",
            'alamat' => "Pakem, Bondowoso",
            'gender' => 'L',
            'pendidikan' => 'S1',
            'agama' => 'Islam',
            'telepon' => '082234432343',
            'username' => 'sinta',
            'password' => Hash::make('123456')
        ]);
    }
}
