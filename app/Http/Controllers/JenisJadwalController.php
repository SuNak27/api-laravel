<?php

namespace App\Http\Controllers;

use App\Models\DetailJenisJadwal;
use App\Models\JenisJadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class JenisJadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jenisJadwal = JenisJadwal::join('users', 'jenis_jadwals.lastupdate_user', 'users.id')
            ->where('jenis_jadwals.deleted_at', null)->select('jenis_jadwals.id_jenis_jadwal', 'jenis_jadwals.kode_jenis_jadwal', 'jenis_jadwals.nama_jenis_jadwal', 'users.name as lastupdate_user')->get();

        foreach ($jenisJadwal as $j) {
            $j['jadwal'] = DetailJenisJadwal::join('jenis_jadwals', 'detail_jenis_jadwals.id_jenis_jadwal', '=', 'jenis_jadwals.id_jenis_jadwal')
                ->join('shifts', 'detail_jenis_jadwals.id_shift', '=', 'shifts.id_shift')
                ->select('detail_jenis_jadwals.id_detail_jenis_jadwal', 'detail_jenis_jadwals.hari')
                ->where('detail_jenis_jadwals.id_jenis_jadwal', $j->id_jenis_jadwal)
                ->orderBy(DB::raw("FIELD(detail_jenis_jadwals.hari ,'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')"))
                ->groupBy('detail_jenis_jadwals.hari')
                ->get();

            foreach ($j['jadwal'] as $s) {
                $s['shift'] = DetailJenisJadwal::join('jenis_jadwals', 'detail_jenis_jadwals.id_jenis_jadwal', '=', 'jenis_jadwals.id_jenis_jadwal')
                    ->join('shifts', 'shifts.id_shift', '=', 'detail_jenis_jadwals.id_shift')
                    ->select('shifts.nama_shift', 'shifts.jam_masuk', 'shifts.jam_keluar')
                    ->where('detail_jenis_jadwals.hari', $s->hari)
                    ->where('detail_jenis_jadwals.id_jenis_jadwal', $j->id_jenis_jadwal)
                    ->get();
            }
        }

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jenisJadwal
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
            'kode_jenis_jadwal' => 'required',
            'nama_jenis_jadwal' => 'required',
            'jadwal' => 'required',
            'jadwal.*.hari' => 'required',
            'jadwal.*.shift' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $response = [
                'success' => false,
                'message' => 'Terdapat data yang salah atau kosong',
                'error' => $error
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
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
