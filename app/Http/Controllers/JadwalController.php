<?php

namespace App\Http\Controllers;

use App\Models\DetailJadwal;
use App\Models\DetailUnit;
use App\Models\Jadwal;
use App\Models\Unit;
use Illuminate\Database\QueryException;
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
        $unit = DetailUnit::join('units', 'detail_units.id_unit', '=', 'units.id')
            ->select('detail_units.id_unit as id_unit', 'units.nama_unit', DB::raw('count(id_karyawan) as jumlah_karyawan'))
            ->where('status', '1')
            ->groupBy('id_unit')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $unit
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
        try {
            $id_jabatan = DB::table('detail_jabatans')->where('id_karyawan', $request[0]['id_karyawan'])->where('status', '1')->first()->id;
            $id_unit = DB::table('detail_units')->where('id_karyawan', $request[0]['id_karyawan'])->where('status', '1')->first()->id;

            $jadwal = $request->all();
            foreach ($jadwal as $key => $value) {

                $check = Jadwal::where('id_karyawan', $value['id_karyawan'])->where('tanggal', $value['tanggal'])->where('bulan', $value['bulan'])->first();

                if ($check) {
                    $id_jadwal = $check->id;
                } else {
                    $jadwal = Jadwal::create([
                        'id_karyawan' => $value['id_karyawan'],
                        'id_tahun' => $value['id_tahun'],
                        'id_jabatan' => $id_jabatan,
                        'id_unit' => $id_unit,
                        'tanggal' => $value['tanggal'],
                        'bulan' => $value['bulan'],
                    ]);
                    $id_jadwal = $jadwal->id;
                }

                $checkDetail = DetailJadwal::where('id_jadwal', $id_jadwal)->where('id_shift', $value['id_shift'])->first();

                if (!$checkDetail) {
                    DetailJadwal::create([
                        'id_jadwal' => $id_jadwal,
                        'id_shift' => $value['id_shift'],
                    ]);
                }
            }

            $response = [
                'success' => true,
                'message' => 'Berhasil',
            ];


            return response()->json($response, Response::HTTP_CREATED);
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
            $j["detail"] = DB::table('detail_jadwals')->leftJoin("shifts", "detail_jadwals.id_shift", "=", "shifts.id")->select("shifts.id", "shifts.kode_shift", "shifts.nama_shift", "shifts.jam_masuk", "shifts.jam_keluar")->where("id_jadwal", $j['id'])->get();
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
        Jadwal::findOrFail($id);
        $new_jadwal = $request->all();

        DetailJadwal::where('id_jadwal', $id)->delete();

        foreach ($new_jadwal as $key => $value) {
            DetailJadwal::create([
                'id_jadwal' => $id,
                'id_shift' => $new_jadwal[$key]['id_shift'],
            ]);
        }

        $response = [
            'success' => true,
            'message' => 'Berhasil',
        ];

        return response()->json($response, Response::HTTP_CREATED);
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

    public function JadwalUnit($id_unit)
    {
    }

    public function karyawan($id_karyawan, $bulan, $id_tahun)
    {
        $jadwal = Jadwal::join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where('jadwals.id_tahun', $id_tahun)
            ->where('jadwals.bulan', $bulan)
            ->select('jadwals.tanggal', 'jadwals.id', 'setting_tahuns.tahun as tahun')
            ->orderBy('jadwals.tanggal', 'asc')
            ->get();

        $result = [];
        foreach ($jadwal as $j) {
            $j["detail"] = DB::table('detail_jadwals')->leftJoin("shifts", "detail_jadwals.id_shift", "=", "shifts.id")->select("shifts.id", "shifts.kode_shift", "shifts.nama_shift", "shifts.jam_masuk", "shifts.jam_keluar")->where("id_jadwal", $j['id'])->orderBy('shifts.jam_masuk')->get();
            array_push($result, $j);
        }

        $karyawan = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('detail_jabatans', 'jadwals.id_jabatan', '=', 'detail_jabatans.id')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->join('detail_units', 'jadwals.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where('jadwals.bulan', $bulan)
            ->where('jadwals.id_tahun', $id_tahun)
            ->select('karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'detail_jabatans.status as status_jabatan', 'detail_units.status as status_unit')
            ->groupBy('jadwals.id_karyawan')
            ->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'karyawan' => $karyawan,
            'detail' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function bulanTahun()
    {
        $jadwal = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('detail_units', 'jadwals.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->join('detail_jabatans', 'jadwals.id_jabatan', '=', 'detail_jabatans.id')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
            ->select('jadwals.id', 'jadwals.id_karyawan', 'jadwals.id_tahun', 'karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'setting_tahuns.tahun as tahun', 'jadwals.bulan')
            ->orderBy('jadwals.id_karyawan')
            ->groupBy('jadwals.id_tahun', 'jadwals.bulan', 'jadwals.id_karyawan')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function check($id_karyawan, $id_shift, $tanggal)
    {
        $jadwal = Jadwal::join('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where('jadwals.tanggal', $tanggal)
            ->where('detail_jadwals.id_shift', $id_shift)
            ->first();

        if ($jadwal) {
            $response = [
                'success' => true,
                'message' => 'Ada Jadwal',
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Tidak ada Jadwal',
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }

    public function checkShift($id_karyawan, $tanggal)
    {
        $jadwal = Jadwal::join('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where('jadwals.tanggal', $tanggal)
            ->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
