<?php

namespace App\Http\Controllers;

use App\Models\DetailJabatan;
use App\Models\Jabatan;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $jabatan = Jabatan::join('users', 'jabatans.lastupdate_user', 'users.id')->where('deleted_at', null)->select('jabatans.id_jabatan', 'jabatans.nama_jabatan', 'users.name as lastupdate_user')->get();
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
        $validator = Validator::make($request->all(), [
            'nama_jabatan' => 'required',
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

        $validator = Validator::make($request->all(), [
            'nama_jabatan' => 'required',
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

            $jabatan->update($request->all());
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function downloadJabatan()
    {
        $fileName = 'jabatan.csv';
        $jabatan = Jabatan::all();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        );

        $columns = array('ID', 'Nama Jabatan');

        $callback = function () use ($jabatan, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($jabatan as $task) {
                $row['ID']  = $task->id;
                $row['Nama Jabatan']  = $task->nama_jabatan;

                fputcsv($file, array($row['ID'], $row['Nama Jabatan']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Belum terpakai
    public function detailJabatan($id_karyawan)
    {
        // $jabatan = DetailJabatan::join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
        //     ->select('detail_jabatans.id_jabatan')
        //     ->where('id_karyawan', $id_karyawan)
        //     ->where('status', '1')
        //     ->first();

        $unit = Unit::join('detail_units', 'units.id', '=', 'detail_units.id_unit')
            ->select('detail_units.id_unit')
            ->where('id_karyawan', $id_karyawan)
            ->where('status', '1')
            ->first();

        // $detail = [
        //     'id_jabatan' => $jabatan->id_jabatan,
        //     'id_unit' => $unit->id_unit
        // ];


        // $response = [
        //     'success' => true,
        //     'message' => 'Berhasil',
        //     'data' => $detail
        // ];
        // return response()->json($response, Response::HTTP_OK);
    }
}
