<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PangkatGolongan extends Model
{
    use HasFactory;

    protected $guarded = ['id_pangkat'];
    protected $primaryKey = 'id_pangkat';
}
