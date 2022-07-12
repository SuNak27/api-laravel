<?php

namespace App\Http\Controllers;

use App\Models\DetailLembur;
use App\Models\Lembur;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LemburController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lembur = DetailLembur::join("lemburs", "detail_lemburs.id_lembur", "=", "lemburs.id")
            ->join('karyawans', 'lemburs.id_karyawan', '=', 'karyawans.id')
            ->select(
                "lemburs.id as id_lembur",
                "karyawans.nama as nama_karyawan",
                "lemburs.tanggal",
                "lemburs.jam_mulai",
                "lemburs.jam_akhir",
                "lemburs.keterangan",
                "detail_lemburs.tgl_pengajuan",
                "detail_lemburs.tgl_disetujui",
                "detail_lemburs.keterangan as catatan",
            )
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $lembur
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
            'jam_mulai' => 'required',
            'jam_akhir' => 'required',
            'keterangan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $lembur = Lembur::create($request->all());

            DetailLembur::create([
                'id_lembur' => $lembur->id,
                'tgl_pengajuan' => date('Y-m-d'),
            ]);

            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $lembur
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
        $lembur = DetailLembur::join("lemburs", "detail_lemburs.id_lembur", "=", "lemburs.id")
            ->join('karyawans', 'lemburs.id_karyawan', '=', 'karyawans.id')
            ->select(
                "lemburs.id as id_lembur",
                "karyawans.nama as nama_karyawan",
                "lemburs.tanggal",
                "lemburs.jam_mulai",
                "lemburs.jam_akhir",
                "lemburs.keterangan",
                "detail_lemburs.tgl_pengajuan",
                "detail_lemburs.tgl_disetujui",
                "detail_lemburs.keterangan as catatan",
            )
            ->where('lemburs.id', $id)
            ->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $lembur
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
        $lembur = Lembur::findOrFail($id);
        try {
            $lembur->update($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $lembur
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
