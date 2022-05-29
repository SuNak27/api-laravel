<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailJadwal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['shift', 'jadwal'];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
