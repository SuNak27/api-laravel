<?php

namespace App\Http\Controllers;

use App\Models\AturanPresensi;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $presensi = Presensi::join("karyawans", "presensis.id_karyawan", "=", "karyawans.id")
            ->join("detail_jabatans", "karyawans.id_jabatan", "=", "detail_jabatans.id")
            ->join("jabatans", "detail_jabatans.id_jabatan", "=", "jabatans.id")
            ->join("detail_units", "karyawans.id_unit", "=", "detail_units.id")
            ->join("units", "detail_units.id_unit", "=", "units.id")
            ->join("shifts", "presensis.id_shift", "=", "shifts.id")
            ->select("presensis.id", "karyawans.id as id_karyawan", "karyawans.nama", "presensis.id_shift as id_shift", "jabatans.nama_jabatan", "units.nama_unit", "presensis.tanggal", "presensis.jam_masuk", "presensis.mode_absen", "presensis.jam_keluar", "presensis.status", "presensis.keterangan", "shifts.nama_shift")->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $presensi
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
            'jam' => 'required',
            'mode_absen' => 'required',
            'id_shift' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $jam_masuk = gmdate('H:i:s', $request->jam + (7 * 60 * 60));
            $tanggal = gmdate('Y-m-d', $request->jam + (7 * 60 * 60));
            $check = Jadwal::join('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
                ->where('jadwals.id_karyawan', $request->id_karyawan)
                ->where('jadwals.tanggal', $tanggal)
                ->where('detail_jadwals.id_shift', $request->id_shift)
                ->first();

            if ($request->jam_keluar == null) {
                $jam_keluar = null;
            } else {
                $jam_keluar = gmdate('H:i:s', $request->jam_keluar + (7 * 60 * 60));
            }

            if ($check != null) {
                $shift = Shift::where('id', $request->id_shift)->first();

                $aturan = AturanPresensi::first();
                $masuk = new Carbon($shift->jam_masuk);

                if ($masuk->diffInMinutes($jam_masuk, false) > $aturan->terlambat) {
                    if ($request->status) {
                        $status = $request->status;
                    } else {
                        $status = "Telat";
                    }
                } else {
                    if ($request->status) {
                        $status = $request->status;
                    } else {
                        $status = "Hadir";
                    }
                }

                $data = [
                    'id_karyawan' => $request->id_karyawan,
                    'id_shift' => $request->id_shift,
                    'tanggal' => $tanggal,
                    'jam_masuk' => $jam_masuk,
                    'jam_keluar' => $jam_keluar,
                    'status' => $status,
                    'mode_absen' => $request->mode_absen,
                    'keterangan' => $request->keterangan
                ];

                $presensi = Presensi::create($data);
                $response = [
                    'success' => true,
                    'message' => 'Berhasil',
                    'data' => $presensi
                ];

                return response()->json($response, Response::HTTP_CREATED);
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Gagal',
                    'data' => 'Jadwal tidak ditemukan'
                ];

                return response()->json($response, Response::HTTP_BAD_REQUEST);
            }
        } catch (QueryException $e) {
            return response()->json(['message' => "Failed " . $e->errorInfo], Response::HTTP_UNPROCESSABLE_ENTITY);
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
        $presensi = Presensi::join("karyawans", "presensis.id_karyawan", "=", "karyawans.id")
            ->join("detail_jabatans", "karyawans.id_jabatan", "=", "detail_jabatans.id")
            ->join("jabatans", "detail_jabatans.id_jabatan", "=", "jabatans.id")
            ->join("detail_units", "karyawans.id_unit", "=", "detail_units.id")
            ->join("units", "detail_units.id_unit", "=", "units.id")
            ->select("presensis.id", "karyawans.id as id_karyawan", "karyawans.nama", "presensis.id_shift", "jabatans.nama_jabatan", "units.nama_unit", "presensis.tanggal", "presensis.jam_masuk", "presensis.jam_keluar", "presensis.status", "presensis.mode_absen", "presensis.keterangan")
            ->where("presensis.id", $id)->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $presensi
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
        $presensi = Presensi::findOrFail($id);

        try {
            $jam_masuk = gmdate('H:i:s', $request->jam + (7 * 60 * 60));
            $tanggal = gmdate('Y-m-d', $request->jam + (7 * 60 * 60));
            $jam_keluar = gmdate('H:i:s', $request->jam_keluar + (7 * 60 * 60));
            $data = $request->all();
            if ($request->jam_keluar) {
                $data['jam_keluar'] = $jam_keluar;
            }

            if ($request->jam) {
                $data['jam_masuk'] = $jam_masuk;
                $data['tanggal'] = $tanggal;
                unset($data['jam']);
            }

            Presensi::where('id', $id)->update($data);

            $presensi = Presensi::find($id)->first();
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $presensi
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json(['message' => "Failed " . $e->errorInfo], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
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

    public function id_presensi($id_karyawan, $tanggal)
    {
        $presensi = Presensi::where("id_karyawan", $id_karyawan)
            ->where("tanggal", $tanggal)
            ->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $presensi
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function karyawan($id_karyawan)
    {
        $presensi = Presensi::join("shifts", "presensis.id_shift", "=", "shifts.id")
            ->where("id_karyawan", $id_karyawan)
            ->select("presensis.*", "shifts.nama_shift")
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $presensi
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function updateWeb(Request $request, $id)
    {

        $presensi = Presensi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_karyawan' => 'required',
            'jam' => 'required',
            'mode_absen' => 'required',
            'id_shift' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $jam_masuk = gmdate('H:i:s', $request->jam + (7 * 60 * 60));
            $tanggal = gmdate('Y-m-d', $request->jam + (7 * 60 * 60));
            $check = Jadwal::join('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
                ->where('jadwals.id_karyawan', $request->id_karyawan)
                ->where('jadwals.tanggal', $tanggal)
                ->where('detail_jadwals.id_shift', $request->id_shift)
                ->first();

            if ($request->jam_keluar == null) {
                $jam_keluar = null;
            } else {
                $jam_keluar = gmdate('H:i:s', $request->jam_keluar + (7 * 60 * 60));
            }

            if ($check != null) {
                $shift = Shift::where('id', $request->id_shift)->first();

                $aturan = AturanPresensi::first();
                $masuk = new Carbon($shift->jam_masuk);

                if ($masuk->diffInMinutes($jam_masuk, false) > $aturan->terlambat) {
                    if ($request->status) {
                        $status = $request->status;
                    } else {
                        $status = "Telat";
                    }
                } else {
                    if ($request->status) {
                        $status = $request->status;
                    } else {
                        $status = "Hadir";
                    }
                }

                $data = [
                    'id_karyawan' => $request->id_karyawan,
                    'id_shift' => $request->id_shift,
                    'tanggal' => $tanggal,
                    'jam_masuk' => $jam_masuk,
                    'jam_keluar' => $jam_keluar,
                    'status' => $status,
                    'mode_absen' => $request->mode_absen,
                    'keterangan' => $request->keterangan
                ];

                $presensi->update($data);
                $response = [
                    'success' => true,
                    'message' => 'Berhasil',
                    'data' => $presensi
                ];

                return response()->json($response, Response::HTTP_CREATED);
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Gagal',
                    'data' => 'Jadwal tidak ditemukan'
                ];

                return response()->json($response, Response::HTTP_BAD_REQUEST);
            }
        } catch (QueryException $e) {
            return response()->json(['message' => "Failed " . $e->errorInfo], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
