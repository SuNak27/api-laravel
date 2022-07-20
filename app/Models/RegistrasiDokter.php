<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrasiDokter extends Model
{
    use HasFactory;

    protected $guarded = ['id_str'];
    protected $primaryKey = 'id_str';
}
