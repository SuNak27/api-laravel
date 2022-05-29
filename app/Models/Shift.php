<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function shift()
    {
        return DB::table('shifts')->join("setting_tahuns", "shifts.id_tahun", "=", "setting_tahuns.id")->select("shifts.*", "setting_tahuns.tahun")->get();
    }

    public function tahun()
    {
        return $this->belongsTo(SettingTahun::class);
    }

    public function detailJadwal()
    {
        return $this->hasMany(DetailJadwal::class);
    }
}
