<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id_detail_penugasan'];
    protected $primaryKey = 'id_detail_penugasan';
}
