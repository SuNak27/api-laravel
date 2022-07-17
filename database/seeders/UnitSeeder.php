<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::create([
            'nama_unit' => 'UGD',
        ]);
        Unit::create([
            'nama_unit' => 'Poli Gigi',
        ]);
        Unit::create([
            'nama_unit' => 'IGD',
        ]);
        Unit::create([
            'nama_unit' => 'Poli Jantung',
        ]);
    }
}
