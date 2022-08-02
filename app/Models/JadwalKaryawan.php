<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKaryawan extends Model
{
    use HasFactory;

    protected $guarded = ['id_jadwal_karyawan'];
    protected $primaryKey = 'id_jadwal_karyawan';
}
