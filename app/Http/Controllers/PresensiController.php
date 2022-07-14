<?php

namespace App\Http\Controllers;

use App\Models\AturanPresensi;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\Shift;
use Carbon\Carbon;
use Dotenv\Parser\Value;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->select("presensis.id", "karyawans.id as id_karyawan", "karyawans.nama", "presensis.id_shift as id_shift", "jabatans.nama_jabatan", "units.nama_unit", "presensis.tanggal", "presensis.jam_masuk", "presensis.mode_absen", "presensis.jam_keluar", "presensis.status", "presensis.keterangan", "shifts.nama_shift")
            ->orderBy("presensis.tanggal", "desc")
            ->get();

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
            'mode_absen' => 'required',
            'id_shift' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {

            // if ($request->tanggal != null) {
            $tanggal = $request->tanggal;
            $jam_masuk = $request->jam_masuk;
            // } else {
            //     $jam_masuk = gmdate('H:i:s', $request->jam + (7 * 60 * 60));
            //     $tanggal = gmdate('Y-m-d', $request->jam + (7 * 60 * 60));
            // }
            $check = Jadwal::join('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
                ->where('jadwals.id_karyawan', $request->id_karyawan)
                ->where('jadwals.tanggal', $tanggal)
                ->where('detail_jadwals.id_shift', $request->id_shift)
                ->first();

            if ($request->jam_keluar == null) {
                $jam_keluar = null;
            } else {
                $jam_keluar = date("H:i", strtotime($request->jam_keluar));;
                // $jam_keluar = gmdate('H:i:s', $request->jam_keluar + (7 * 60 * 60));
            }

            if ($check != null) {
                $checkHadir = Presensi::where('tanggal', $tanggal)->where('id_shift', $request->id_shift)->where('id_karyawan', $request->id_karyawan)->first();
                if ($checkHadir == null) {
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
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
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
                        'message' => 'Karyawan telah hadir pada hari dan shift saat ini',
                        'data' => null
                    ];

                    return response()->json($response, Response::HTTP_BAD_REQUEST);
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan',
                    'data' => null
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

        $validator = Validator::make($request->all(), [
            'id_karyawan' => 'required',
            'mode_absen' => 'required',
            'id_shift' => 'required',
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

        try {
            if ($request->tanggal != null) {
                $tanggal = $request->tanggal;
                $jam_masuk = date("H:i:s", strtotime($request->jam_masuk));
            } else {
                $jam_masuk = gmdate('H:i:s', $request->jam + (7 * 60 * 60));
                $tanggal = gmdate('Y-m-d', $request->jam + (7 * 60 * 60));
            }

            $check = Jadwal::join('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
                ->where('jadwals.id_karyawan', $request->id_karyawan)
                ->where('jadwals.tanggal', $tanggal)
                ->where('detail_jadwals.id_shift', $request->id_shift)
                ->first();

            if ($request->jam_keluar == null) {
                $jam_keluar = null;
            } else if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $request->jam_keluar)) {
                $jam_keluar = date("H:i:s", strtotime($request->jam_keluar));;
            } else {
                $jam_keluar = gmdate('H:i:s', $request->jam_keluar + (7 * 60 * 60));
            }

            // $jam_keluar = gmdate('H:i:s', $request->jam_keluar + (7 * 60 * 60));

            if ($check != null) {

                $checkHadir = Presensi::where('tanggal', $tanggal)->where('id_shift', $request->id_shift)->where('id_karyawan', $request->id_karyawan)->first();
                if ($checkHadir == null) {
                    // return ("belum hadir");
                    $shift = Shift::where('id', $request->id_shift)->first();

                    $aturan = AturanPresensi::first();
                    $masuk = new Carbon($shift->jam_masuk);

                    if ($masuk->diffInMinutes($jam_masuk, false) > $aturan->terlambat) {
                        $data['status'] = "Telat";
                    } else {
                        $data['status'] = "Hadir";
                    }

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

                    return response()->json($response, Response::HTTP_CREATED);
                } else {
                    if ($checkHadir->jam_masuk != $jam_masuk || $checkHadir->jam_keluar != $jam_keluar) {
                        // return ([$checkHadir->jam_masuk, $jam_masuk, $checkHadir->jam_keluar, $jam_keluar]);

                        $shift = Shift::where('id', $request->id_shift)->first();

                        $aturan = AturanPresensi::first();
                        $masuk = new Carbon($shift->jam_masuk);

                        if ($masuk->diffInMinutes($jam_masuk, false) > $aturan->terlambat) {
                            $data['status'] = "Telat";
                        } else {
                            $data['status'] = "Hadir";
                        }

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

                        return response()->json($response, Response::HTTP_CREATED);
                    } else {
                        // return ("Sudah absen");
                        $response = [
                            'success' => false,
                            'message' => 'Karyawan telah hadir pada hari dan shift saat ini',
                            'data' => null
                        ];

                        return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
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

    public function rekap()
    {
        $presensi = Presensi::join("karyawans", "presensis.id_karyawan", "=", "karyawans.id")
            ->select("karyawans.nama", "presensis.id_karyawan")
            ->groupBy("presensis.id_karyawan")
            ->get();

        foreach ($presensi as $key => $value) {
            $presensi[$key]->presensi = Presensi::join("karyawans", "presensis.id_karyawan", "=", "karyawans.id")
                ->where("presensis.id_karyawan", $value->id_karyawan)
                ->select("presensis.tanggal", "presensis.status")
                ->get();
        }

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $presensi
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
