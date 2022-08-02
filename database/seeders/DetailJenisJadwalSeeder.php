<?php

namespace Database\Seeders;

use App\Models\DetailJenisJadwal;
use Illuminate\Database\Seeder;

class DetailJenisJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DetailJenisJadwal::create([
            'hari' => "Senin",
            'id_jenis_jadwal' => 1,
            'id_shift' => 1,
            'lastupdate_user' => 1
        ]);

        DetailJenisJadwal::create([
            'hari' => "Selasa",
            'id_jenis_jadwal' => 1,
            'id_shift' => 1,
            'lastupdate_user' => 1
        ]);

        DetailJenisJadwal::create([
            'hari' => "Rabu",
            'id_jenis_jadwal' => 1,
            'id_shift' => 1,
            'lastupdate_user' => 1
        ]);

        DetailJenisJadwal::create([
            'hari' => "Kamis",
            'id_jenis_jadwal' => 1,
            'id_shift' => 1,
            'lastupdate_user' => 1
        ]);

        DetailJenisJadwal::create([
            'hari' => "Jumat",
            'id_jenis_jadwal' => 1,
            'id_shift' => 1,
            'lastupdate_user' => 1
        ]);
    }
}
