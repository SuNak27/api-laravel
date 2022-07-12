<?php

namespace App\Http\Controllers;

use App\Models\DetailIzin;
use App\Models\Izin;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class IzinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $izin = DetailIzin::join("izins", "detail_izins.id_izin", "=", "izins.id")
            ->join('karyawans', 'izins.id_karyawan', '=', 'karyawans.id')
            ->select(
                "izins.id as id_izin",
                "karyawans.nama as nama_karyawan",
                "izins.status",
                "izins.tanggal_mulai",
                "izins.tanggal_selesai",
                "izins.keterangan",
                "detail_izins.tgl_pengajuan",
                "detail_izins.tgl_disetujui",
                "detail_izins.keterangan as catatan",
            )
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $izin
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
            'status' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'keterangan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $izin = Izin::create($request->all());

            DetailIzin::create([
                'id_izin' => $izin->id,
                'tgl_pengajuan' => date('Y-m-d'),
            ]);

            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $izin
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
        $izin = DetailIzin::join("izins", "detail_izins.id_izin", "=", "izins.id")
            ->join('karyawans', 'izins.id_karyawan', '=', 'karyawans.id')
            ->select(
                "izins.id as id_izin",
                "karyawans.nama as nama_karyawan",
                "izins.status",
                "izins.tanggal_mulai",
                "izins.tanggal_selesai",
                "izins.keterangan",
                "detail_izins.tgl_pengajuan",
                "detail_izins.tgl_disetujui",
                "detail_izins.keterangan as catatan",
            )
            ->where("izins.id", $id)
            ->first();
        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $izin
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

        $izin = Izin::findOrFail($id);
        try {
            $izin->update($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $izin
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
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
