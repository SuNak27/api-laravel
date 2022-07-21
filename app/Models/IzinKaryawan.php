<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinKaryawan extends Model
{
    use HasFactory;

    protected $guarded = ['id_izin_karyawan'];
    protected $primaryKey = 'id_izin_karyawan';
}
