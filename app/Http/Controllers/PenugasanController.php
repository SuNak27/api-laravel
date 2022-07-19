<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PenugasanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $penugasan = Penugasan::join('users', 'users.id', '=', 'penugasans.lastupdate_user')
            ->where('deleted_at', null)
            ->select('penugasans.*', 'users.name')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Success',
            'data' => $penugasan
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
            'tujuan' => 'required',
            'kegiatan' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_akhir' => 'required',
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
            // Last Update User (UPDATABLE)
            $request['lastupdate_user'] = 1;

            $checkTanggalMulai = Penugasan::where('tanggal_mulai', $request->tanggal_mulai)->where('deleted_at', null)->first();

            if ($checkTanggalMulai) {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan sudah mengajukan penugasan pada tanggal tersebut',
                    'data' => $checkTanggalMulai
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $penugasan = Penugasan::create($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $penugasan
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Penugasan  $penugasan
     * @return \Illuminate\Http\Response
     */
    public function show(Penugasan $penugasan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Penugasan  $penugasan
     * @return \Illuminate\Http\Response
     */
    public function edit(Penugasan $penugasan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Penugasan  $penugasan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_karyawan' => 'required',
            'tujuan' => 'required',
            'kegiatan' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_akhir' => 'required',
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
            // Last Update User (UPDATABLE)
            $request['lastupdate_user'] = 1;

            $checkTanggalMulai = Penugasan::where('tanggal_mulai', $request->tanggal_mulai)->where('deleted_at', null)->first();

            if ($checkTanggalMulai && $request->tanggal_mulai != $penugasan->tanggal_mulai) {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan sudah mengajukan penugasan pada tanggal tersebut',
                    'data' => $checkTanggalMulai
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $checkPenugasan = Penugasan::where('id_karyawan', $request->id_karyawan)
                ->whereDate('tanggal_mulai', '<=', $request->tanggal_mulai)
                ->whereDate('tanggal_akhir', '>=', $request->tanggal_akhir)
                ->where('deleted_at', null)
                ->first();

            if ($penugasan->tanggal_mulai && $request->tanggal_mulai != $penugasan->tanggal_mulai) {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan sedang mengajukan penugasan pada rentang tanggal tersebut',
                    'data' => $checkPenugasan
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $penugasan->update($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $penugasan
            ];
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Penugasan  $penugasan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Penugasan $penugasan)
    {
        //
    }
}
