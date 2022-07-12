<?php

namespace App\Http\Controllers;

use App\Models\DetailPerdin;
use App\Models\Perdin;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PerdinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perdin = DetailPerdin::join("perdins", "detail_perdins.id_perdin", "=", "perdins.id")
            ->join('karyawans', 'perdins.id_karyawan', '=', 'karyawans.id')
            ->select(
                "perdins.id as id_perdin",
                "karyawans.nama as nama_karyawan",
                "perdins.tanggal_mulai",
                "perdins.tanggal_selesai",
                "perdins.kegiatan",
                "detail_perdins.tgl_pengajuan",
                "detail_perdins.tgl_disetujui",
                "detail_perdins.keterangan as catatan",
            )
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $perdin
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
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kegiatan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $perdin = Perdin::create($request->all());

            DetailPerdin::create([
                'id_perdin' => $perdin->id,
                'tgl_pengajuan' => date('Y-m-d'),
            ]);

            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $perdin
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
        $perdin = DetailPerdin::join("perdins", "detail_perdins.id_perdin", "=", "perdins.id")
            ->join('karyawans', 'perdins.id_karyawan', '=', 'karyawans.id')
            ->select(
                "perdins.id as id_perdin",
                "karyawans.nama as nama_karyawan",
                "perdins.tanggal_mulai",
                "perdins.tanggal_selesai",
                "perdins.kegiatan",
                "detail_perdins.tgl_pengajuan",
                "detail_perdins.tgl_disetujui",
                "detail_perdins.keterangan as catatan",
            )
            ->where('perdins.id', $id)
            ->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $perdin
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
        $perdin = Perdin::findOrFail($id);
        try {
            $perdin->update($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $perdin
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
