<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisIzin extends Model
{
    use HasFactory;

    protected $guarded = ['id_jenis_izin'];
    protected $primaryKey = 'id_jenis_izin';
}
