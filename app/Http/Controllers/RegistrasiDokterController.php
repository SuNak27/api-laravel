<?php

namespace App\Http\Controllers;

use App\Models\RegistrasiDokter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RegistrasiDokterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $registrasi = RegistrasiDokter::join('users', 'registrasi_dokters.lastupdate_user', 'users.id')
            ->join('karyawans', 'registrasi_dokters.id_karyawan', 'karyawans.id_karyawan')
            ->where('registrasi_dokters.deleted_at', null)
            ->select('registrasi_dokters.*', 'karyawans.nama_karyawan', 'users.name as lastupdate_user')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $registrasi
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
            'no_str' => 'required',
            'tanggal_awal' => 'required',
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
            $checkRegistrasi = RegistrasiDokter::where('id_karyawan', $request->id_karyawan)
                ->whereDate('tanggal_awal', '<=', $request->tanggal_awal)
                ->whereDate('tanggal_akhir', '>=', $request->tanggal_akhir)
                ->where('deleted_at', null)
                ->first();

            if ($checkRegistrasi) {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan sudah terdaftar pada rentang tanggal tersebut',
                    'data' => $checkRegistrasi
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $registrasi = RegistrasiDokter::create($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $registrasi
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
        $registrasi = RegistrasiDokter::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_karyawan' => 'required',
            'no_str' => 'required',
            'tanggal_awal' => 'required',
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
            $checkRegistrasi = RegistrasiDokter::where('id_karyawan', $request->id_karyawan)
                ->whereDate('tanggal_awal', '<=', $request->tanggal_awal)
                ->whereDate('tanggal_akhir', '>=', $request->tanggal_akhir)
                ->where('id_str', '!=', $id)
                ->where('deleted_at', null)
                ->first();

            if ($checkRegistrasi) {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan sudah terdaftar pada rentang tanggal tersebut',
                    'data' => $checkRegistrasi
                ];
                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $registrasi->update($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $registrasi
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
