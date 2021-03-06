<?php

namespace App\Http\Controllers;

use App\Models\DetailJabatan;
use App\Models\DetailUnit;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
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
        $karyawan = Karyawan::join('detail_jabatans', 'karyawans.id_karyawan', '=', 'detail_jabatans.id_karyawan')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id_jabatan')
            ->join('units', 'detail_jabatans.id_unit', '=', 'units.id_unit')
            ->select('karyawans.*', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
            ->orderBy('karyawans.id_karyawan', 'asc')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $karyawan
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
            'nik_karyawan' => 'required|numeric',
            'nama_karyawan' => 'required',
            'id_jabatan' => 'required',
            'id_unit' => 'required',
            'tanggal_lahir' => 'required',
            'status_kawin' => 'required',
            'alamat' => 'required',
            'gender' => 'required',
            'pendidikan' => 'required',
            'telepon' => 'required|numeric',
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

            $data = [
                'nama_karyawan' => $request->nama_karyawan,
                'nik_karyawan' => $request->nik_karyawan,
                'tanggal_lahir' => $request->tanggal_lahir,
                'status_kawin' => $request->status_kawin,
                'alamat' => $request->alamat,
                'gender' => $request->gender,
                'pendidikan' => $request->pendidikan,
                'telepon' => $request->telepon,
                'agama' => $request->agama,
            ];

            if ($request->file('image')) {
                $data['image'] = $request->file('image')->store('karyawans', 'public');
            }

            $karyawan = Karyawan::create($data);

            $detailJabatan = [
                'id_jabatan' => $request->id_jabatan,
                'id_unit' => $request->id_unit,
                'id_karyawan' => $karyawan->id,
                // Belum FIX (UPDATEABLE)
                'id_pangkat' => 1,
                'lastupdate_user' => 1,
            ];

            DetailJabatan::create($detailJabatan);

            $karyawan = Karyawan::join('detail_jabatans', 'karyawans.id_karyawan', '=', 'detail_jabatans.id_karyawan')
                ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id_jabatan')
                ->join('units', 'detail_jabatans.id_unit', '=', 'units.id_unit')
                ->select('karyawans.*', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
                ->where('karyawans.id_karyawan', $karyawan->id)->first();

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
        $karyawan = Karyawan::join('detail_jabatans', 'karyawans.id_karyawan', '=', 'detail_jabatans.id_karyawan')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id_jabatan')
            ->join('units', 'detail_jabatans.id_unit', '=', 'units.id_unit')
            ->select('karyawans.*', 'detail_jabatans.id_unit', 'detail_jabatans.id_jabatan', 'jabatans.nama_jabatan as jabatan', 'units.nama_unit as unit')
            ->where('karyawans.id_karyawan', $id)->first();


        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $karyawan
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
        $karyawan = Karyawan::where('id_karyawan', $id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nama_karyawan' => 'required',
            'nik_karyawan' => 'required|numeric',
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
            // $oldImage = $karyawan['image'];
            $data = [
                'nik_karyawan' => $request->nik_karyawan,
                'nama_karyawan' => $request->nama_karyawan,
                'tanggal_lahir' => $request->tanggal_lahir,
                'status_kawin' => $request->status_kawin,
                'alamat' => $request->alamat,
                'gender' => $request->gender,
                'pendidikan' => $request->pendidikan,
                'telepon' => $request->telepon,
                'agama' => $request->agama,
                'username' => $request->username,
            ];

            // if ($request->file('image')) {
            //     if ($request->image) {
            //         if ($oldImage != "") {
            //             Storage::delete('public/' . $oldImage);
            //         }
            //     }
            //     $data['image'] = $request->file('image')->store('karyawans', 'public');
            // }


            // dd($oldImage);
            Karyawan::where('id_karyawan', $id)->update($data);

            $checkJabatan = DetailJabatan::where('id_karyawan', $karyawan->id_karyawan)->where('deleted_at', null)->first();


            if ($checkJabatan->id_jabatan != (int)$request->id_jabatan || $checkJabatan->id_unit != (int)$request->id_unit) {
                DetailJabatan::where('id_detail_jabatan', $checkJabatan->id_detail_jabatan)->update(['deleted_at' => now()]);
                $detailJabatan = [
                    'id_jabatan' => $request->id_jabatan,
                    'id_unit' => $request->id_unit,
                    'id_karyawan' => $karyawan->id_karyawan,
                    // Belum Fix (UPDATEABLE)
                    'id_pangkat' => 1,
                    'lastupdate_user' => 1,
                ];
                DetailJabatan::create($detailJabatan);
            }

            $karyawan = Karyawan::where('id_karyawan', $id)->firstOrFail();

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

    // public function statistic()
    // {
    //     $jabatan = Karyawan::join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
    //         ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
    //         ->select("jabatans.nama_jabatan as name", DB::raw('count(jabatans.id) as value'))
    //         ->groupBy('jabatans.id')
    //         ->get();

    //     $gender = Karyawan::select("gender as name", DB::raw('count(gender) as value'))
    //         ->groupBy('gender')
    //         ->get();

    //     $tanggal = date("Y-m-d");

    //     $jadwal = Jadwal::select(DB::raw('count(jadwals.id_karyawan) as jadwal_hari_ini'))->where('jadwals.tanggal', $tanggal)->first();

    //     $presensi = Presensi::select(DB::raw('count(id) as hadir_hari_ini'))->where('tanggal', $tanggal)->first();

    //     $jumlah_jadwal = new stdClass();
    //     $jumlah_jadwal->jadwal = $jadwal->jadwal_hari_ini;
    //     $jumlah_jadwal->hadir = $presensi->hadir_hari_ini;

    //     $response = [
    //         "success" => true,
    //         "message" => "Berhasil",
    //         "jumlah_jabatan" => $jabatan,
    //         "jumlah_karyawan" => $gender,
    //         "jumlah_jadwal" => $jumlah_jadwal
    //     ];

    //     return response()->json($response, Response::HTTP_OK);
    // }

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

    // public function uploadKaryawan(Request $request)
    // {
    //     $file = $request->file('uploaded_file');
    //     if ($file) {
    //         $filename = $file->getClientOriginalName();
    //         $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
    //         $tempPath = $file->getRealPath();
    //         $fileSize = $file->getSize(); //Get size of uploaded file in bytes
    //         //Check for file extension and size
    //         $this->checkUploadedFileProperties($extension, $fileSize);
    //         //Where uploaded file will be stored on the server
    //         $location = 'uploads'; //Created an "uploads" folder for that
    //         // Upload file
    //         $file->move($location, $filename);
    //         // In case the uploaded file path is to be stored in the database
    //         $filepath = public_path($location . "/" . $filename);
    //         // Reading file
    //         $file = fopen($filepath, "r");
    //         $importData_arr = array(); // Read through the file and store the contents as an array
    //         $i = 0;
    //         //Read the contents of the uploaded file
    //         while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
    //             $num = count($filedata);
    //             // Skip first row (Remove below comment if you want to skip the first row)
    //             if ($i == 0) {
    //                 $i++;
    //                 continue;
    //             }
    //             for ($c = 0; $c < $num; $c++) {
    //                 $importData_arr[$i][] = $filedata[$c];
    //             }
    //             $i++;
    //         }
    //         fclose($file); //Close after reading
    //         $j = 0;
    //         foreach ($importData_arr as $importData) {
    //             $j++;
    //             try {
    //                 DB::beginTransaction();
    //                 $data = [
    //                     'id_unit' => $importData[0],
    //                     'id_jabatan' => $importData[1],
    //                     'nama' => $importData[2],
    //                     'nik' => $importData[3],
    //                     'tanggal_lahir' => $importData[4],
    //                     'status_kawin' => $importData[5],
    //                     'alamat' => $importData[6],
    //                     'gender' => $importData[7],
    //                     'pendidikan' => $importData[8],
    //                     'telepon' => $importData[9],
    //                     'agama' => $importData[10],
    //                     'username' => $importData[11],
    //                 ];
    //                 $tanggalLahir = Carbon::parse($data['tanggal_lahir']);
    //                 $data['tanggal_lahir'] = $tanggalLahir->format('Y-m-d');
    //                 $karyawan = Karyawan::create($data);
    //                 $detailJabatan = [
    //                     'id_jabatan' => $data['id_jabatan'],
    //                     'id_karyawan' => $karyawan->id,
    //                     'status' => "1",
    //                 ];
    //                 $detailUnit = [
    //                     'id_unit' => $data['id_unit'],
    //                     'id_karyawan' => $karyawan->id,
    //                     'status' => "1",
    //                 ];

    //                 $dJabatan = DetailJabatan::create($detailJabatan);
    //                 $dUnit = DetailUnit::create($detailUnit);

    //                 $newKaryawan = [
    //                     'id_jabatan' => $dJabatan->id,
    //                     'id_unit' => $dUnit->id,
    //                 ];
    //                 $karyawan->update($newKaryawan);
    //                 DB::commit();
    //             } catch (\Exception $e) {
    //                 //throw $th;
    //                 DB::rollBack();
    //             }
    //         }
    //         return response()->json([
    //             'message' => "$j records successfully uploaded"
    //         ], Response::HTTP_CREATED);
    //     } else {
    //         //no file was uploaded
    //         throw new \Exception('No file was uploaded', Response::HTTP_BAD_REQUEST);
    //     }
    // }
    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 2097152; // Uploaded file size limit is 2mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            } else {
                throw new \Exception('No file was uploaded', Response::HTTP_REQUEST_ENTITY_TOO_LARGE); //413 error
            }
        } else {
            throw new \Exception('Invalid file extension', Response::HTTP_UNSUPPORTED_MEDIA_TYPE); //415 error
        }
    }

    public function downloadImportKaryawan()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/storage/excel/karyawan.csv";

        $headers = array(
            'Content-Type: text/csv',
        );

        return response()->download($file, 'import-karyawan.csv', $headers);
    }
}
