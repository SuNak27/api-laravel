<?php

namespace App\Http\Controllers;

use App\Models\DetailIzin;
use App\Models\Izin;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

// class IzinController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      *
//      * @return \Illuminate\Http\Response
//      */
//     public function index()
//     {
//         $izin = Izin::join('karyawans', 'izins.id_karyawan', '=', 'karyawans.id')
//             ->select(
//                 "izins.id as id_izin",
//                 "izins.id_karyawan",
//                 "karyawans.nama as nama_karyawan",
//                 "izins.jenis_izin",
//                 "izins.status_izin",
//                 "izins.tanggal_mulai",
//                 "izins.tanggal_selesai",
//                 "izins.tgl_pengajuan",
//                 "izins.tgl_persetujuan",
//                 "izins.keterangan_izin",
//                 "izins.keterangan_persetujuan",
//             )
//             ->get();

//         $response = [
//             'success' => true,
//             'message' => 'Berhasil',
//             'data' => $izin
//         ];

//         return response()->json($response, Response::HTTP_OK);
//     }

//     /**
//      * Show the form for creating a new resource.
//      *
//      * @return \Illuminate\Http\Response
//      */
//     public function create()
//     {
//         //
//     }

//     /**
//      * Store a newly created resource in storage.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @return \Illuminate\Http\Response
//      */
//     public function store(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'id_karyawan' => 'required',
//             'jenis_izin' => 'required',
//             'tanggal_mulai' => 'required',
//             'tanggal_selesai' => 'required',
//         ]);

//         if ($validator->fails()) {
//             return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
//         }

//         try {
//             $id_karyawan = $request->id_karyawan;
//             $tanggal = $request->tanggal_mulai;
//             $check = Izin::where('id_karyawan', $id_karyawan)->where('tanggal_mulai', $tanggal)->select('id')->first();

//             if ($check) {
//                 $response = [
//                     'success' => false,
//                     'message' => 'Anda telah mengajukan izin pada tanggal tersebut, Silahkan hubungi admin untuk konfirmasi jika perubahan data',
//                 ];

//                 return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
//             } else {
//                 $request['tgl_pengajuan'] = date('Y-m-d');
//                 $request['status_izin'] = "Pengajuan";
//                 $izin = Izin::create($request->all());

//                 $response = [
//                     'success' => true,
//                     'message' => 'Berhasil',
//                     'data' => $izin
//                 ];

//                 return response()->json($response, Response::HTTP_CREATED);
//             }
//         } catch (QueryException $e) {
//             return response()->json(['message' => "Failed " . $e->errorInfo], Response::HTTP_UNPROCESSABLE_ENTITY);
//         }
//     }

//     /**
//      * Display the specified resource.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function show($id)
//     {
//         $izin = Izin::join('karyawans', 'izins.id_karyawan', '=', 'karyawans.id')
//             ->select(
//                 "izins.id as id_izin",
//                 "karyawans.nama as nama_karyawan",
//                 "izins.jenis_izin",
//                 "izins.status_izin",
//                 "izins.tanggal_mulai",
//                 "izins.tanggal_selesai",
//                 "izins.tgl_pengajuan",
//                 "izins.tgl_persetujuan",
//                 "izins.keterangan_izin",
//                 "izins.keterangan_persetujuan",
//             )
//             ->where('izins.id', $id)
//             ->get();

//         $response = [
//             'success' => true,
//             'message' => 'Berhasil',
//             'data' => $izin
//         ];

//         return response()->json($response, Response::HTTP_OK);
//     }

//     /**
//      * Show the form for editing the specified resource.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function edit($id)
//     {
//         //
//     }

//     /**
//      * Update the specified resource in storage.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function update(Request $request, $id)
//     {
//         $izin = Izin::findOrFail($id);
//         try {
//             $izin->update($request->all());
//             $response = [
//                 'success' => true,
//                 'message' => 'Berhasil',
//                 'data' => $izin
//             ];
//             return response()->json($response, Response::HTTP_CREATED);
//         } catch (QueryException $e) {
//             $response = [
//                 'success' => false,
//                 'message' => $e->getMessage(),
//                 'data' => []
//             ];
//             return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
//         }
//     }

//     /**
//      * Remove the specified resource from storage.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function destroy($id)
//     {
//         //
//     }
// }
