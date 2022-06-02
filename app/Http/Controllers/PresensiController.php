<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
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
            ->join("jadwals", function ($join) {
                $join->on("karyawans.id", "=", "jadwals.id_karyawan")
                    ->on("presensis.tanggal", "=", "jadwals.tanggal");
            })
            ->join('detail_jadwals', "jadwals.id", '=', "detail_jadwals.id_jadwal")
            ->join('shifts', "detail_jadwals.id_shift", "=", "shifts.id")
            ->select("presensis.id", "karyawans.id as id_karyawan", "karyawans.nama",  "jabatans.nama_jabatan", "units.nama_unit", "presensis.tanggal", "presensis.jam_masuk", "presensis.jam_keluar", "presensis.status", "presensis.keterangan", "shifts.nama_shift", "shifts.kode_shift")->get();

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
            'tanggal' => 'required',
            'jam_masuk' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $presensi = Presensi::create($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $presensi
            ];

            return response()->json($response, Response::HTTP_CREATED);
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
            ->select("presensis.id", "karyawans.id as id_karyawan", "karyawans.nama",  "jabatans.nama_jabatan", "units.nama_unit", "presensis.tanggal", "presensis.jam_masuk", "presensis.jam_keluar", "presensis.status", "presensis.keterangan")
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
            $presensi->update($request->all());
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
}
