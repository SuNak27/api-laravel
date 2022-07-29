<?php

namespace Database\Seeders;

use App\Models\JadwalKaryawan;
use Illuminate\Database\Seeder;

class JadwalKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JadwalKaryawan::create([
            'id_karyawan' => 1,
            'id_jenis_jadwal' => 1,
            'lastupdate_user' => 1
        ]);
    }
}
