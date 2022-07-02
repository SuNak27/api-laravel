<?php

namespace App\Http\Controllers;

use App\Models\DetailJabatan;
use App\Models\DetailUnit;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use stdClass;
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
            'telepon' => 'required|numeric',
            'image'  => 'required|mimes:png,jpg,jpeg,gif|max:2305',
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
                'agama' => $request->agama,
                'username' => $request->username,
            ];

            if ($request->file('image')) {
                $data['image'] = $request->file('image')->store('karyawans', 'public');
            }

            $karyawan = Karyawan::create($data);
            $detailJabatan = [
                'id_jabatan' => $request->id_jabatan,
                'id_karyawan' => $karyawan->id,
                'status' => "1",
            ];
            $detailUnit = [
                'id_unit' => $request->id_unit,
                'id_karyawan' => $karyawan->id,
                'status' => "1",
            ];

            $dJabatan = DetailJabatan::create($detailJabatan);
            $dUnit = DetailUnit::create($detailUnit);

            $newKaryawan = [
                'id_jabatan' => $dJabatan->id,
                'id_unit' => $dUnit->id,
            ];
            $karyawan->update($newKaryawan);

            $karyawan = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
                ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
                ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
                ->join('units', 'detail_units.id_unit', '=', 'units.id')
                ->select('karyawans.*', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
                ->where('karyawans.id', $karyawan->id)->first();

            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $karyawan
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {

            $response = [
                'success' => false,
                'message' => 'Gagal',
                'data' => $e->errorInfo
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
        $admin = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
            ->leftJoin('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->leftJoin('units', 'detail_units.id_unit', '=', 'units.id')
            ->select('karyawans.*', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
            ->where('karyawans.id', $id)
            ->first();


        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $admin
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
        // dd($request->all());
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
            'telepon' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $oldImage = $karyawan->image;
            $data = [
                'nama' => $request->nama,
                'nik' => $request->nik,
                'tanggal_lahir' => $request->tanggal_lahir,
                'status_kawin' => $request->status_kawin,
                'alamat' => $request->alamat,
                'gender' => $request->gender,
                'pendidikan' => $request->pendidikan,
                'telepon' => $request->telepon,
                'agama' => $request->agama,
                'username' => $request->username,
            ];

            if ($request->file('image')) {
                // if ($request->Image) {
                //     if ($oldImage != "") {
                //         Storage::delete($oldImage);
                //     }
                // }
                $data['image'] = $request->file('image')->store('karyawans', 'public');
            }


            // dd($oldImage);
            $karyawan->update($data);

            $checkJabatan = DetailJabatan::where('id_karyawan', $karyawan->id)->where('status', '1')->first();

            if ($checkJabatan->id_jabatan != $request->id_jabatan) {
                $checkJabatan->update(['status' => '0']);
                $detailJabatan = [
                    'id_jabatan' => $request->id_jabatan,
                    'id_karyawan' => $karyawan->id,
                    'status' => "1",
                ];
                $dJabatan = DetailJabatan::create($detailJabatan);

                $newKaryawan = [
                    'id_jabatan' => $dJabatan['id'],
                ];

                $karyawan->update($newKaryawan);
            }

            $checkUnit = DetailUnit::where('id_karyawan', $karyawan->id)->where('status', '1')->first();

            if ($checkUnit->id_unit != $request->id_unit) {
                $checkUnit->update([
                    'status' => "0"
                ]);
                $detailUnit = [
                    'id_unit' => $request->id_unit,
                    'id_karyawan' => $karyawan->id,
                    'status' => "1",
                ];
                $dUnit = DetailUnit::create($detailUnit);

                $newKaryawan = [
                    'id_unit' => $dUnit['id'],
                ];

                $karyawan->update($newKaryawan);
            }

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

    public function statistic()
    {
        $jabatan = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->select("jabatans.nama_jabatan as name", DB::raw('count(jabatans.id) as value'))
            ->groupBy('jabatans.id')
            ->get();

        $gender = Karyawan::select("gender as name", DB::raw('count(gender) as value'))
            ->groupBy('gender')
            ->get();

        $tanggal = date("Y-m-d");

        $jadwal = Jadwal::select(DB::raw('count(jadwals.id_karyawan) as jadwal_hari_ini'))->where('jadwals.tanggal', $tanggal)->first();

        $presensi = Presensi::select(DB::raw('count(id) as hadir_hari_ini'))->where('tanggal', $tanggal)->first();

        $jumlah_jadwal = new stdClass();
        $jumlah_jadwal->jadwal = $jadwal->jadwal_hari_ini;
        $jumlah_jadwal->hadir = $presensi->hadir_hari_ini;

        $response = [
            "success" => true,
            "message" => "Berhasil",
            "jumlah_jabatan" => $jabatan,
            "jumlah_karyawan" => $gender,
            "jumlah_jadwal" => $jumlah_jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function karyawanUnit($id_unit)
    {
        $karyawan = Karyawan::join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->select('karyawans.id as id_karyawan', 'karyawans.nama', 'units.nama_unit')
            ->where('units.id', $id_unit)->get();

        $response = [
            "success" => true,
            "message" => "Berhasil",
            "data" => $karyawan
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
