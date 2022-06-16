<?php

namespace App\Http\Controllers;

use App\Models\SettingTahun;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingTahunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settingTahun = SettingTahun::orderBy('status')->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $settingTahun
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
            $check = SettingTahun::where('status', '1')->first();
            if ($request->status == 1) {
                $check->status = "0";
                $check->save();
            }
            $settingTahun = SettingTahun::create($request->all());

            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $settingTahun
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];

            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
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
        try {
            $settingTahun = SettingTahun::findOrFail($id);

            $check = SettingTahun::where('status', '1')->first();
            if ($request->status == 1 && $check != null) {
                if ($check->id != $settingTahun->id) {
                    $check->status = "0";
                    $check->save();
                }
            }

            $settingTahun->update($request->all());

            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'data' => $settingTahun
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];

            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
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

    public function tahun_aktif()
    {
        $settingTahun = SettingTahun::where('status', '1')->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $settingTahun
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
