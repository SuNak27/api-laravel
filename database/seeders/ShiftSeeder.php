<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shift::create([
            'kode_shift' => "P-001",
            'nama_shift' => "Pagi",
            'jam_masuk' => "08:00",
            'jam_keluar' => "12:00",
        ]);

        Shift::create([
            'kode_shift' => "S-001",
            'nama_shift' => "Siang",
            'jam_masuk' => "13:00",
            'jam_keluar' => "17:00",
        ]);

        Shift::create([
            'kode_shift' => "M-001",
            'nama_shift' => "Malam",
            'jam_masuk' => "18:00",
            'jam_keluar' => "22:00",
        ]);
    }
}
