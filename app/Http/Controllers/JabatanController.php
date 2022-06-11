<?php

namespace App\Http\Controllers;

use App\Models\DetailJabatan;
use App\Models\Jabatan;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jabatan = Jabatan::all();
        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jabatan
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
            $jabatan = Jabatan::create($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $jabatan
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
        $jabatan = Jabatan::findOrFail($id);
        try {
            $jabatan->update($request->all());
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $jabatan
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

    public function detailJabatan($id_karyawan)
    {
        $jabatan = DetailJabatan::join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->select('detail_jabatans.id_jabatan')
            ->where('id_karyawan', $id_karyawan)
            ->where('status', '1')
            ->first();

        $unit = Unit::join('detail_units', 'units.id', '=', 'detail_units.id_unit')
            ->select('detail_units.id_unit')
            ->where('id_karyawan', $id_karyawan)
            ->where('status', '1')
            ->first();

        $detail = [
            'id_jabatan' => $jabatan->id_jabatan,
            'id_unit' => $unit->id_unit
        ];


        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $detail
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
