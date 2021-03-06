<?php

namespace App\Http\Controllers;

use App\Models\DetailGaji;
use App\Models\DetailGajiKaryawan;
use App\Models\Gaji;
use App\Models\Jabatan;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class GajiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gaji = Gaji::join("units", "units.id", "=", "gajis.id_unit")
            ->select("gajis.*", "units.nama_unit")
            ->get();

        $result = [];
        foreach ($gaji as $j) {
            $j["detail"] = DB::table('detail_gajis')->leftJoin("jabatans", "detail_gajis.id_jabatan", "=", "jabatans.id")->select("jabatans.id", "jabatans.nama_jabatan", "detail_gajis.gaji")->where("id_gaji", $j['id'])->get();
            array_push($result, $j);
        }



        $response = [
            'success' => true,
            'message' => 'List of all Gaji',
            'data' => $gaji
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
        try {
            $gaji = Gaji::create([
                'id_unit' => $request->unit
            ]);

            $detail = $request->detail;
            foreach ($detail as $d) {
                DetailGaji::create([
                    'id_gaji' => $gaji->id,
                    'id_jabatan' => $d['id'],
                    'gaji' => $request->gaji
                ]);
            }

            $response = [
                'success' => true,
                'message' => 'Gaji created successfully',
                'data' => $gaji
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => 'Gaji creation failed',
                'data' => []
            ];

            return response()->json($response, Response::HTTP_BAD_REQUEST);
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
        $gaji = Gaji::join("units", "units.id", "=", "gajis.id_unit")
            ->select("units.nama_unit")
            ->where("gajis.id", $id)
            ->first();

        $result = DB::table('detail_gajis')->leftJoin("jabatans", "detail_gajis.id_jabatan", "=", "jabatans.id")->select("jabatans.id", "jabatans.nama_jabatan", "detail_gajis.gaji")->where("id_gaji", $id)->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'unit' => $gaji->nama_unit,
            'data' => $result
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
        //
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

    public function gajiAll()
    {
        $gaji = DetailGajiKaryawan::join("karyawans", "karyawans.id", "=", "detail_gaji_karyawans.id_karyawan")
            ->join("detail_gajis", "detail_gaji_karyawans.id_detail_gaji", "=", "detail_gajis.id")
            ->join("gajis", "gajis.id", "=", "detail_gajis.id_gaji")
            ->join("jabatans", "jabatans.id", "=", "detail_gajis.id_jabatan")
            ->join("units", "units.id", "=", "gajis.id_unit")
            ->select("detail_gaji_karyawans.id", "karyawans.nama", "jabatans.nama_jabatan", "detail_gajis.gaji", "units.nama_unit", "detail_gaji_karyawans.denda", "detail_gaji_karyawans.bulan")
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $gaji
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function checkGaji($id_unit, $id_jabatan)
    {
        $id_unit = Unit::join('detail_units', 'units.id', '=', 'detail_units.id_unit')
            ->select('units.id', 'units.nama_unit')
            ->where('detail_units.id', $id_unit)
            ->first();

        $id_jabatan = Jabatan::join('detail_jabatans', 'jabatans.id', '=', 'detail_jabatans.id_jabatan')
            ->select('jabatans.id', 'jabatans.nama_jabatan')
            ->where('detail_jabatans.id', $id_jabatan)
            ->first();

        $gaji = Gaji::join("detail_gajis", "detail_gajis.id_gaji", "=", "gajis.id")
            ->select("detail_gajis.*", "gajis.id_unit")
            ->where("gajis.id_unit", $id_unit->id)
            ->where("detail_gajis.id_jabatan", $id_jabatan->id)
            ->first();


        if ($gaji != null) {
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $gaji
            ];

            return response()->json($response, Response::HTTP_OK);
        } else {
            $response = [
                'success' => false,
                'message' => 'Tidak ada data gaji',
            ];

            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function detailGaji($bulan)
    {
        $gaji = DetailGajiKaryawan::join("detail_gajis", "detail_gaji_karyawans.id_detail_gaji", "=", "detail_gajis.id")
            ->join("gajis", "detail_gajis.id_gaji", "=", "gajis.id")
            ->join("jabatans", "detail_gajis.id_jabatan", "=", "jabatans.id")
            ->join("karyawans", "detail_gaji_karyawans.id_karyawan", "=", "karyawans.id")
            ->join("units", "gajis.id_unit", "=", "units.id")
            ->select("karyawans.nama", "jabatans.nama_jabatan", "detail_gaji_karyawans.bulan", "units.nama_unit", "detail_gajis.gaji", "detail_gaji_karyawans.denda")
            ->where("detail_gaji_karyawans.bulan", $bulan)
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $gaji
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function gajiKaryawan(Request $request)
    {
        try {
            $gaji = [
                'id_detail_gaji' => $request->id_detail_gaji,
                'id_karyawan' => $request->id_karyawan,
                'bulan' => $request->bulan,
                'denda' => 0
            ];

            $detail = DetailGajiKaryawan::create($gaji);

            $response = [
                'success' => true,
                'message' => 'Gaji created successfully',
                'data' => $detail
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => 'Gaji creation failed',
                'data' => []
            ];

            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }
    }
}
