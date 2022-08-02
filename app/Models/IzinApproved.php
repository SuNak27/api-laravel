<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinApproved extends Model
{
    use HasFactory;

    protected $guarded = ['id_detail_izin'];
    protected $primaryKey = 'id_detail_izin';
}
