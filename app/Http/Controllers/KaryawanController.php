<?php

namespace App\Http\Controllers;

use App\Models\DetailJabatan;
use App\Models\DetailUnit;
use App\Models\Karyawan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->select('karyawans.*', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
            ->orderBy('karyawans.id', 'asc')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $admin
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
            'nama' => 'required',
            'nik' => 'required|numeric',
            'id_jabatan' => 'required',
            'id_unit' => 'required',
            'tanggal_lahir' => 'required',
            'status_kawin' => 'required',
            'alamat' => 'required',
            'gender' => 'required',
            'pendidikan' => 'required',
            'telepon' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $data = [
                'nama' => $request->nama,
                'nik' => $request->nik,
                'tanggal_lahir' => $request->tanggal_lahir,
                'status_kawin' => $request->status_kawin,
                'alamat' => $request->alamat,
                'gender' => $request->gender,
                'pendidikan' => $request->pendidikan,
                'telepon' => $request->telepon,
            ];

            $karyawan = Karyawan::create($data);
            $detailJabatan = [
                'id_jabatan' => $request->id_jabatan,
                'id_karyawan' => $karyawan->id,
            ];
            $detailUnit = [
                'id_unit' => $request->id_unit,
                'id_karyawan' => $karyawan->id,
            ];

            $dJabatan = DetailJabatan::create($detailJabatan);
            $dUnit = DetailUnit::create($detailUnit);

            Karyawan::where('id', $karyawan->id)->update([
                'id_jabatan' => $dJabatan->id,
                'id_unit' => $dUnit->id,
            ]);

            $karyawan = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
                ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
                ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
                ->join('units', 'detail_units.id_unit', '=', 'units.id')
                ->select('karyawans.*', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
                ->where('karyawans.id', $karyawan->id);


            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $karyawan
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
        $admin = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->join('units', 'karyawans.id_unit', '=', 'units.id')
            ->select('karyawans.*', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
            ->where('karyawans.id', $id)
            ->get();


        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $admin[0]
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
        $karyawan = Karyawan::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'nik' => 'required|numeric',
            'id_jabatan' => 'required',
            'id_unit' => 'required',
            'tanggal_lahir' => 'required',
            'status_kawin' => 'required',
            'alamat' => 'required',
            'gender' => 'required',
            'pendidikan' => 'required',
            'telepon' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $karyawan->update($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $karyawan
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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $karyawan = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
                ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
                ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
                ->join('units', 'karyawans.id_unit', '=', 'units.id')
                ->select('karyawans.id as id_karyawan', 'karyawans.password', 'karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
                ->where('username', $request->username)->first();
            if ($karyawan) {
                if (Hash::check($request->password, $karyawan->password)) {
                    $response = [
                        'success' => true,
                        'message' => 'Berhasil',
                        'data' => $karyawan
                    ];

                    return response()->json($response, Response::HTTP_OK);
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Password Salah'
                    ];

                    return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Karyawan Tidak Ditemukan'
                ];

                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (QueryException $e) {
            return response()->json(['message' => "Failed " . $e->errorInfo], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
