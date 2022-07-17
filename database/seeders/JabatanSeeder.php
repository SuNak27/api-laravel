<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Jabatan::create([
            'nama_jabatan' => 'Karyawan',
        ]);
        Jabatan::create([
            'nama_jabatan' => 'Suster',
        ]);
        Jabatan::create([
            'nama_jabatan' => 'Dokter',
        ]);
        Jabatan::create([
            'nama_jabatan' => 'Manager',
        ]);
    }
}
