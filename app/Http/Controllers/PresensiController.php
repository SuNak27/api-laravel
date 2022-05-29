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
            ->join("jabatans", "karyawans.id_jabatan", "=", "jabatans.id")
            ->join("units", "karyawans.id_unit", "=", "units.id")
            ->select("karyawans.nama", "jabatans.nama_jabatan", "units.nama_unit", "presensis.tanggal", "presensis.jam_masuk", "presensis.jam_keluar", "presensis.status", "presensis.keterangan")->get();

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
            'tanggal' => 'required',
            'jam_masuk' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

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
}
