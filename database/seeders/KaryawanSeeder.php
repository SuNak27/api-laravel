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
    }
}
