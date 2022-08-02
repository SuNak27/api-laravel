<?php

namespace Database\Seeders;

use App\Models\JenisJadwal;
use Illuminate\Database\Seeder;

class JenisJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisJadwal::create([
            'kode_jenis_jadwal' => "J-001",
            'nama_jenis_jadwal' => "Normal Shift Pagi",
            'lastupdate_user' => 1
        ]);

        JenisJadwal::create([
            'kode_jenis_jadwal' => "J-002",
            'nama_jenis_jadwal' => "Normal Shift Siang",
            'lastupdate_user' => 1
        ]);

        JenisJadwal::create([
            'kode_jenis_jadwal' => "J-003",
            'nama_jenis_jadwal' => "Normal Shift Malam",
            'lastupdate_user' => 1
        ]);
    }
}
