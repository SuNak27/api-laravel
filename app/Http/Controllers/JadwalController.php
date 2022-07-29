<?php

namespace App\Http\Controllers;

use App\Models\DetailJenisJadwal;
use App\Models\JadwalKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $jadwal = JadwalKaryawan::join('karyawans', 'karyawans.id_karyawan', '=', 'jadwal_karyawans.id_karyawan')
            ->join('jenis_jadwals', 'jenis_jadwals.id_jenis_jadwal', '=', 'jadwal_karyawans.id_jenis_jadwal')
            ->select('jadwal_karyawans.id_jadwal_karyawan', 'karyawans.id_karyawan', 'karyawans.nama_karyawan', 'jenis_jadwals.nama_jenis_jadwal', 'jadwal_karyawans.created_at as jadwal_dibuat', 'jadwal_karyawans.updated_at as jadwal_diubah', DB::raw('(CASE
            WHEN jadwal_karyawans.deleted_at THEN "Tidak Aktif"
            ELSE "Aktif"
            END) AS status'))
            ->get();

        foreach ($jadwal as $j) {
            $j['jadwal'] = DetailJenisJadwal::join('jenis_jadwals', 'detail_jenis_jadwals.id_jenis_jadwal', '=', 'jenis_jadwals.id_jenis_jadwal')
                ->join('jadwal_karyawans', 'jadwal_karyawans.id_jenis_jadwal', '=', 'jenis_jadwals.id_jenis_jadwal')
                ->join('karyawans', 'jadwal_karyawans.id_karyawan', '=', 'karyawans.id_karyawan')
                ->join('shifts', 'shifts.id_shift', '=', 'detail_jenis_jadwals.id_shift')
                ->select('detail_jenis_jadwals.hari')
                ->where('jadwal_karyawans.id_karyawan', $j->id_karyawan)
                ->orderBy(DB::raw("FIELD(detail_jenis_jadwals.hari ,'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')"))
                ->groupBy('detail_jenis_jadwals.hari')
                ->get();

            foreach ($j['jadwal'] as $s) {
                $s['shift'] = DetailJenisJadwal::join('jenis_jadwals', 'detail_jenis_jadwals.id_jenis_jadwal', '=', 'jenis_jadwals.id_jenis_jadwal')
                    ->join('jadwal_karyawans', 'jadwal_karyawans.id_jenis_jadwal', '=', 'jenis_jadwals.id_jenis_jadwal')
                    ->join('karyawans', 'jadwal_karyawans.id_karyawan', '=', 'karyawans.id_karyawan')
                    ->join('shifts', 'shifts.id_shift', '=', 'detail_jenis_jadwals.id_shift')
                    ->select('shifts.nama_shift', 'shifts.jam_masuk', 'shifts.jam_keluar')
                    ->where('jadwal_karyawans.id_karyawan', $j->id_karyawan)
                    ->where('detail_jenis_jadwals.hari', $s->hari)
                    ->get();
            }
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
