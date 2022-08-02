<?php

namespace App\Http\Controllers;

use App\Models\IzinApproved;
use App\Models\IzinKaryawan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
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
        $izin = IzinKaryawan::join('karyawans', 'izin_karyawans.id_karyawan', 'karyawans.id_karyawan')
            ->join('izin_approveds', 'izin_karyawans.id_izin_karyawan', '=', 'izin_approveds.id_izin_karyawan')
            ->join('jenis_izins', 'izin_karyawans.id_jenis_izin', '=', 'jenis_izins.id_jenis_izin')
            ->join('users', 'users.id', '=', 'izin_karyawans.lastupdate_user')
            ->where('izin_karyawans.deleted_at', null)
            ->select('izin_karyawans.*', 'jenis_izins.nama_jenis_izin', 'karyawans.nama_karyawan', 'izin_approveds.*', 'users.name as lastupdate_user')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Success',
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
            'id_jenis_izin' => 'required',
            'id_karyawan' => 'required',
            'tanggal_awal' => 'required',
            'tanggal_akhir' => 'required',
            'keterangan_izin' => 'required',
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

            $checkTanggalMulai = IzinKaryawan::where('tanggal_awal', $request->tanggal_awal)
                ->where('id_karyawan', $request->id_karyawan)
                ->where('deleted_at', null)
                ->first();


            if ($checkTanggalMulai) {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan sudah mengajukan izin pada tanggal tersebut',
                    'data' => $checkTanggalMulai
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $checkIzin = IzinKaryawan::where('id_karyawan', $request->id_karyawan)
                ->whereDate('tanggal_awal', '<=', $request->tanggal_awal)
                ->whereDate('tanggal_akhir', '>=', $request->tanggal_akhir)
                ->where('deleted_at', null)
                ->first();

            if ($checkIzin) {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan sedang mengajukan izin pada rentang tanggal tersebut',
                    'data' => $checkIzin
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $izinCreate = IzinKaryawan::create($request->all());

            $detailJabatan = IzinKaryawan::join('detail_jabatans', 'izin_karyawans.id_karyawan', '=', 'detail_jabatans.id_karyawan')
                ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id_jabatan')
                ->where('detail_jabatans.deleted_at', null)
                ->first();

            IzinApproved::create([
                'id_izin_karyawan' => $izinCreate->id_izin_karyawan,
                'id_detail_jabatan' => $detailJabatan->id_detail_jabatan,
                'status' => 'Proses',
            ]);
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $izinCreate
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
        $izin = IzinKaryawan::findOrFail($id);

        if ($request->has('status')) {
            $validator = Validator::make($request->all(), [
                'status' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'id_jenis_izin' => 'required',
                'id_karyawan' => 'required',
                'tanggal_awal' => 'required',
                'tanggal_akhir' => 'required',
                'keterangan_izin' => 'required',
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
                IzinApproved::where('id_izin_karyawan', $id)
                    ->where('deleted_at', null)
                    ->update([
                        'status' => $request->status,
                        'keterangan_acc' => $request->keterangan_acc,
                    ]);

                $izin = IzinKaryawan::join('izin_approveds', 'izin_karyawans.id_izin_karyawan', '=', 'izin_approveds.id_izin_karyawan')
                    ->where('izin_karyawans.id_izin_karyawan', $id)
                    ->select('izin_karyawans.*', 'izin_approveds.status', 'izin_approveds.keterangan_acc')
                    ->first();
            } else {
                $checkTanggalMulai = IzinKaryawan::where('tanggal_awal', $request->tanggal_awal)
                    ->where('id_karyawan', $request->id_karyawan)
                    ->where('deleted_at', null)->first();

                if ($checkTanggalMulai && $request->tanggal_awal != $izin->tanggal_awal) {
                    $response = [
                        'success' => false,
                        'message' => 'Karyawan sudah mengajukan izin pada tanggal tersebut',
                        'data' => $checkTanggalMulai
                    ];
                    return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $checkIzin = IzinKaryawan::where('id_karyawan', $request->id_karyawan)
                    ->whereDate('tanggal_awal', '<=', $request->tanggal_awal)
                    ->whereDate('tanggal_akhir', '>=', $request->tanggal_akhir)
                    ->where('id_izin_karyawan', '!=', $id)
                    ->where('deleted_at', null)
                    ->first();

                if ($checkIzin) {
                    $response = [
                        'success' => false,
                        'message' => 'Karyawan sedang mengajukan izin pada rentang tanggal tersebut',
                        'data' => $checkIzin
                    ];
                    return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                if ($request->status || $request->keterangan_acc) {
                    IzinApproved::where('id_izin_karyawan', $id)
                        ->where('deleted_at', null)
                        ->update([
                            'status' => $request->status,
                            'keterangan_acc' => $request->keterangan_acc,
                        ]);
                }

                $izin->update($request->all());
            }

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
