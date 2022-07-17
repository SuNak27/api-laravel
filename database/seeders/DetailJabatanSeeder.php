<?php

namespace Database\Seeders;

use App\Models\DetailJabatan;
use Illuminate\Database\Seeder;

class DetailJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DetailJabatan::create([
            'id_karyawan' => 1,
            'id_jabatan' => 1,
            'id_unit' => 1,
            'id_pangkat' => 1,
        ]);
    }
}
