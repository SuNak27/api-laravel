<?php

namespace App\Http\Controllers;

use App\Models\DetailJadwal;
use App\Models\Jadwal;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwal = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('jabatans', 'karyawans.id_jabatan', '=', 'jabatans.id')
            ->join('units', 'karyawans.id_unit', '=', 'units.id')
            ->join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
            ->select('jadwals.tanggal', 'jadwals.id', 'karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'setting_tahuns.tahun as tahun')
            ->get();


        $result = [];
        foreach ($jadwal as $j) {

            $j["detail"] = DB::table('detail_jadwals')->leftJoin("shifts", "detail_jadwals.id_shift", "=", "shifts.id")->select("shifts.id", "shifts.nama_shift", "shifts.jam_masuk", "shifts.jam_keluar")->where("id_jadwal", $j['id'])->get();

            array_push($result, $j);
        }

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_karyawan' => 'required',
            'id_tahun' => 'required',
            'id_shift' => 'required',
            'tanggal' => 'required',
            'bulan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $jadwal = Jadwal::create([
                'id_karyawan' => $request->id_karyawan,
                'id_tahun' => $request->id_tahun,
                'tanggal' => $request->tanggal,
                'bulan' => $request->bulan,
            ]);

            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $jadwal
            ];

            DetailJadwal::create([
                'id_jadwal' => $jadwal->id,
                'id_shift' => $request->id_shift,
            ]);

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];

            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jadwal = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('jabatans', 'karyawans.id_jabatan', '=', 'jabatans.id')
            ->join('units', 'karyawans.id_unit', '=', 'units.id')
            ->join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
            ->where('jadwals.id_karyawan', $id)
            ->select('jadwals.tanggal', 'jadwals.id', 'karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'setting_tahuns.tahun as tahun')
            ->get();


        $result = [];
        foreach ($jadwal as $j) {
            $j["detail"] = DB::table('detail_jadwals')->leftJoin("shifts", "detail_jadwals.id_shift", "=", "shifts.id")->select("shifts.id", "shifts.nama_shift", "shifts.jam_masuk", "shifts.jam_keluar")->where("id_jadwal", $j['id'])->get();
            array_push($result, $j);
        }

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function bulanTahun($id_karyawan, $bulan, $id_tahun)
    {
        $jadwal = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('jabatans', 'karyawans.id_jabatan', '=', 'jabatans.id')
            ->join('units', 'karyawans.id_unit', '=', 'units.id')
            ->join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where('jadwals.id_tahun', $id_tahun)
            ->where('jadwals.bulan', $bulan)
            ->select('jadwals.tanggal', 'jadwals.id', 'karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'setting_tahuns.tahun as tahun')
            ->get();

        $result = [];
        foreach ($jadwal as $j) {
            $j["detail"] = DB::table('detail_jadwals')->leftJoin("shifts", "detail_jadwals.id_shift", "=", "shifts.id")->select("shifts.id", "shifts.nama_shift", "shifts.jam_masuk", "shifts.jam_keluar")->where("id_jadwal", $j['id'])->get();
            array_push($result, $j);
        }

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
