<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisJadwal extends Model
{
    use HasFactory;

    protected $guarded = ['id_jenis_jadwal'];
    protected $primaryKey = 'id_jenis_jadwal';
}
