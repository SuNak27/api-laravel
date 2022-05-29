<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function karyawans()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function tahun()
    {
        return $this->belongsTo(SettingTahun::class);
    }

    public function detailJadwals()
    {
        return $this->hasMany(DetailJadwal::class);
    }
}
