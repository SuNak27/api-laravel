<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\SettingTahun;
use App\Models\Shift;
use App\Models\Unit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MasterDataController extends Controller
{
    public function index()
    {
        $jabatan = Jabatan::all();
        $unit = Unit::all();
        $setting_tahun = SettingTahun::all();
        $settingTahun = SettingTahun::where('status', '1')->first();
        $shift = Shift::join("setting_tahuns", "shifts.id_tahun", "=", "setting_tahuns.id")->select("shifts.*", "setting_tahuns.tahun", "setting_tahuns.status")->get();
        $karyawan = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->select('karyawans.*', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
            ->orderBy('karyawans.id', 'asc')
            ->get();

        $response = [
            "success" => true,
            "message" => "Berhasil",
            'karyawan' => $karyawan,
            "jabatan" => $jabatan,
            "unit" => $unit,
            "shift" => $shift,
            "setting_tahun" => $setting_tahun,
            "tahun_aktif" => $settingTahun
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
