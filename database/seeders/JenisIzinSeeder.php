<?php

namespace Database\Seeders;

use App\Models\JenisIzin;
use Illuminate\Database\Seeder;

class JenisIzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisIzin::create([
            'nama_jenis_izin' => 'Izin Terlambat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        JenisIzin::create([
            'nama_jenis_izin' => 'Izin Pulang Cepat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        JenisIzin::create([
            'nama_jenis_izin' => 'Izin Sakit',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        JenisIzin::create([
            'nama_jenis_izin' => 'Izin Cuti',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
