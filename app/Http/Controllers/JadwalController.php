<?php

namespace App\Http\Controllers;

use App\Models\DetailJadwal;
use App\Models\Jadwal;
use Illuminate\Http\Request;
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
        // $jadwal = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
        //     ->join('jabatans', 'karyawans.id_jabatan', '=', 'jabatans.id')
        //     ->join('units', 'karyawans.id_unit', '=', 'units.id')
        //     ->join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
        //     ->leftJoin('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
        //     ->leftJoin('shifts', 'detail_jadwals.id_shift', '=', 'shifts.id')
        //     ->select('jadwals.tanggal', 'karyawans.nama as nama_karyawan', 'detail_jadwals.*', 'shifts.*', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'setting_tahuns.tahun as tahun')
        //     ->get();

        $jadwal = Jadwal::all();


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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
}
