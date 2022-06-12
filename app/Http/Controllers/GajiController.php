<?php

namespace App\Http\Controllers;

use App\Models\DetailGaji;
use App\Models\Gaji;
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
}
