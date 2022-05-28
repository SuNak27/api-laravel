<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingTahun extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function shift()
    {
        return $this->hasMany(Shift::class);
    }
}
