<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\PenugasanDetail;
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
        $penugasan = Penugasan::join('karyawans', 'penugasans.id_karyawan', 'karyawans.id_karyawan')
            ->join('penugasan_details', 'penugasans.id_penugasan', '=', 'penugasan_details.id_penugasan')
            ->join('users', 'users.id', '=', 'penugasans.lastupdate_user')
            ->where('penugasans.deleted_at', null)
            ->select('penugasans.*', 'karyawans.nama_karyawan', 'penugasan_details.*', 'users.name as lastupdate_user')
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

            $checkTanggalMulai = Penugasan::where('tanggal_mulai', $request->tanggal_mulai)
                ->where('id_karyawan', $request->id_karyawan)
                ->where('deleted_at', null)
                ->first();

            if ($checkTanggalMulai) {
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

            if ($checkPenugasan) {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan sedang mengajukan penugasan pada rentang tanggal tersebut',
                    'data' => $checkPenugasan
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $penugasan = Penugasan::create($request->all());

            PenugasanDetail::create([
                'id_penugasan' => $penugasan->id_penugasan,
                'status' => 'Proses',
            ]);
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

        if ($request->has('status')) {
            $validator = Validator::make($request->all(), [
                'status' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'id_karyawan' => 'required',
                'tujuan' => 'required',
                'kegiatan' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_akhir' => 'required',
            ]);
        }

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

            if ($request->has('status')) {
                PenugasanDetail::where('id_penugasan', $id)->update([
                    'status' => $request->status,
                    'keterangan_acc' => $request->keterangan_acc,
                ]);

                $penugasan = Penugasan::join('penugasan_details', 'penugasans.id_penugasan', '=', 'penugasan_details.id_penugasan')
                    ->where('penugasans.id_penugasan', $id)
                    ->select('penugasans.*', 'penugasan_details.status', 'penugasan_details.keterangan_acc')
                    ->first();
            } else {
                $checkTanggalMulai = Penugasan::where('tanggal_mulai', $request->tanggal_mulai)
                    ->where('id_karyawan', $request->id_karyawan)
                    ->where('deleted_at', null)->first();

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
                    ->where('id_penugasan', '!=', $id)
                    ->where('deleted_at', null)
                    ->first();

                if ($checkPenugasan) {
                    $response = [
                        'success' => false,
                        'message' => 'Karyawan sedang mengajukan penugasan pada rentang tanggal tersebut',
                        'data' => $checkPenugasan
                    ];
                    return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                if ($request->status || $request->keterangan_acc) {
                    PenugasanDetail::where('id_penugasan', $id)
                        ->where('deleted_at', null)
                        ->update([
                            'status' => $request->status,
                            'keterangan_acc' => $request->keterangan_acc,
                        ]);
                }

                $penugasan->update($request->all());
            }

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
