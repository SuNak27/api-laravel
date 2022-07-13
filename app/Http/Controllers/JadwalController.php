<?php

namespace App\Http\Controllers;

use App\Models\DetailJadwal;
use App\Models\DetailUnit;
use App\Models\Jadwal;
use App\Models\SettingTahun;
use App\Models\Unit;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwal = DetailUnit::join('units', 'detail_units.id_unit', '=', 'units.id')
            ->select('detail_units.id_unit as id_unit', 'units.nama_unit', DB::raw('count(id_karyawan) as jumlah_karyawan'))
            ->where('status', '1')
            ->groupBy('id_unit')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
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

            $dateFormat = 'Y-m-d';
            $dateInput = $request->tanggal;

            $time = strtotime($dateInput);
            $testDate = date($dateFormat, $time);

            if ($dateInput == $testDate) {
                // return 'Valid format' . PHP_EOL;
                $start = Carbon::parse($request->tanggal)->format('Y-m-d');
                $end = Carbon::parse($request->tanggal)->format('Y-m-d');
                // return 'Valid format' . $start;
            } else {
                // return 'Invalid format' . PHP_EOL;
                $start = Carbon::parse($request->tanggal)->startOfMonth()->format('Y-m-d');
                $end = Carbon::parse($request->tanggal)->endOfMonth()->format('Y-m-d');
                // return  'Invalid format' . $start;
            }

            if ($request->tanggal_mulai) {
                $period = CarbonPeriod::create($request->tanggal_mulai, $request->tanggal_akhir);
            } else {
                $period = CarbonPeriod::create($start, $end);
            }
            $dates = [];
            foreach ($period as $date) {
                $check = Jadwal::where('id_karyawan', $request->id_karyawan)->where('tanggal', $date->format('Y-m-d'))->first();


                if ($check == null) {
                    $jadwal = Jadwal::create([
                        'id_karyawan' => $request->id_karyawan,
                        'id_tahun' => $request->id_tahun,
                        'tanggal' => $date->format('Y-m-d')
                    ]);
                    $id_jadwal = $jadwal->id;
                } else {
                    $id_jadwal = $check->id;
                }

                foreach ($request->id_shift as $shift) {
                    $checkDetail = DetailJadwal::where('id_jadwal', $id_jadwal)->where('id_shift', $shift)->first();
                    if (!$checkDetail) {
                        DetailJadwal::create([
                            'id_jadwal' => $id_jadwal,
                            'id_shift' => $shift,
                        ]);
                    }
                }
            }

            $response = [
                'success' => true,
                'message' => 'Berhasil',
            ];


            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
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
        $jadwal = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('jabatans', 'karyawans.id_jabatan', '=', 'jabatans.id')
            ->join('units', 'karyawans.id_unit', '=', 'units.id')
            ->join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
            ->where('jadwals.id_karyawan', $id)
            ->select('jadwals.tanggal', 'jadwals.id', 'karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'setting_tahuns.tahun as tahun')
            ->get();


        $result = [];
        foreach ($jadwal as $j) {
            $j["detail"] = DB::table('detail_jadwals')->leftJoin("shifts", "detail_jadwals.id_shift", "=", "shifts.id")->select("shifts.id", "shifts.kode_shift", "shifts.nama_shift", "shifts.jam_masuk", "shifts.jam_keluar")->where("id_jadwal", $j['id'])->get();
            array_push($result, $j);
        }

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
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
        Jadwal::findOrFail($id);
        $new_jadwal = $request->all();

        DetailJadwal::where('id_jadwal', $id)->delete();

        foreach ($new_jadwal as $key => $value) {
            DetailJadwal::create([
                'id_jadwal' => $id,
                'id_shift' => $new_jadwal[$key]['id_shift'],
            ]);
        }

        $response = [
            'success' => true,
            'message' => 'Berhasil',
        ];

        return response()->json($response, Response::HTTP_CREATED);
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

    public function jadwalUnit($id_unit)
    {
        $unit = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->where('units.id', $id_unit)
            ->where('detail_units.status', '1')
            ->select('units.id', 'units.nama_unit')
            ->first();

        $unitNull = Unit::where("id", $id_unit)->select('id', 'nama_unit')->first();

        $karyawan = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->where('units.id', $id_unit)
            ->where('detail_units.status', '1')
            ->select('karyawans.id as id_karyawan', 'karyawans.nama as nama_karyawan', DB::raw('count(tanggal) as jumlah_jadwal'), DB::raw("DATE_FORMAT(jadwals.tanggal, '%Y-%m') new_date"),  DB::raw('YEAR(jadwals.tanggal) year, MONTH(jadwals.tanggal) month'), 'jadwals.id_tahun')
            ->groupBy('jadwals.id_karyawan', 'year', 'month')
            ->orderBy('jadwals.tanggal', 'DESC')
            ->get();

        if ($unit != null) {
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'id_unit' => $unit->id,
                'unit' => $unit->nama_unit,
                'data' => $karyawan
            ];
        } else {
            $response = [
                'success' => true,
                'message' => 'Berhasil',
                'id_unit' => $unitNull->id,
                'unit' => $unitNull->nama_unit,
                'data' => $karyawan
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }

    public function karyawan($id_karyawan, $bulan, $id_tahun)
    {
        $jadwal = Jadwal::join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where('jadwals.id_tahun', $id_tahun)
            ->where(DB::raw("MONTH(jadwals.tanggal)"), $bulan)
            ->select('jadwals.tanggal', 'jadwals.id', 'setting_tahuns.tahun as tahun')
            ->orderBy('jadwals.tanggal', 'asc')
            ->get();

        $result = [];
        foreach ($jadwal as $j) {
            $j["detail"] = DB::table('detail_jadwals')->leftJoin("shifts", "detail_jadwals.id_shift", "=", "shifts.id")->select("shifts.id", "shifts.kode_shift", "shifts.nama_shift", "shifts.jam_masuk", "shifts.jam_keluar")->where("id_jadwal", $j['id'])->orderBy('shifts.jam_masuk')->get();
            array_push($result, $j);
        }

        $karyawan = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where(DB::raw("MONTH(jadwals.tanggal)"), $bulan)
            ->where('jadwals.id_tahun', $id_tahun)
            ->select('karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'karyawans.image', 'detail_jabatans.status as status_jabatan', 'detail_units.status as status_unit', DB::raw("MONTH(jadwals.tanggal) as bulan"))
            ->groupBy('jadwals.id_karyawan')
            ->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'karyawan' => $karyawan,
            'detail' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function bulanTahun()
    {
        $jadwal = Jadwal::join('karyawans', 'jadwals.id_karyawan', '=', 'karyawans.id')
            ->join('detail_units', 'karyawans.id_unit', '=', 'detail_units.id')
            ->join('units', 'detail_units.id_unit', '=', 'units.id')
            ->join('detail_jabatans', 'karyawans.id_jabatan', '=', 'detail_jabatans.id')
            ->join('jabatans', 'detail_jabatans.id_jabatan', '=', 'jabatans.id')
            ->join('setting_tahuns', 'jadwals.id_tahun', '=', 'setting_tahuns.id')
            ->select('jadwals.id', 'jadwals.id_karyawan', 'jadwals.id_tahun', 'karyawans.nama as nama_karyawan', 'jabatans.nama_jabatan as nama_jabatan', 'units.nama_unit as nama_unit', 'setting_tahuns.tahun as tahun', DB::raw("MONTH(jadwals.tanggal) as bulan"))
            ->orderBy('jadwals.id_karyawan')
            ->groupBy('jadwals.id_tahun', DB::raw("MONTH(jadwals.tanggal)"), 'jadwals.id_karyawan')
            ->get();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function check($id_karyawan, $id_shift, $tanggal)
    {
        $jadwal = Jadwal::join('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where('jadwals.tanggal', $tanggal)
            ->where('detail_jadwals.id_shift', $id_shift)
            ->first();

        if ($jadwal) {
            $response = [
                'success' => true,
                'message' => 'Ada Jadwal',
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Tidak ada Jadwal',
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }

    public function checkShift($id_karyawan, $tanggal)
    {
        $jadwal = Jadwal::join('detail_jadwals', 'jadwals.id', '=', 'detail_jadwals.id_jadwal')
            ->where('jadwals.id_karyawan', $id_karyawan)
            ->where('jadwals.tanggal', $tanggal)
            ->first();

        $response = [
            'success' => true,
            'message' => 'Berhasil',
            'data' => $jadwal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function uploadJadwal(Request $request)
    {
        $file = $request->file('uploaded_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes
            //Check for file extension and size
            $this->checkUploadedFileProperties($extension, $fileSize);
            //Where uploaded file will be stored on the server
            $location = 'uploads'; //Created an "uploads" folder for that
            // Upload file
            $file->move($location, $filename);
            // In case the uploaded file path is to be stored in the database
            $filepath = public_path($location . "/" . $filename);
            // Reading file
            $file = fopen($filepath, "r");
            $importData_arr = array(); // Read through the file and store the contents as an array
            $i = 0;
            //Read the contents of the uploaded file
            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);
                // Skip first row (Remove below comment if you want to skip the first row)
                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file); //Close after reading
            $j = 0;
            foreach ($importData_arr as $importData) {
                $j++;
                try {
                    DB::beginTransaction();
                    $data = [
                        'id_karyawan' => $importData[0],
                        'id_shift' => $importData[1],
                        'id_tahun' => $importData[2],
                        'tanggal' => $importData[3],
                    ];

                    $dateFormat = 'Y-m-d';
                    $dateInput = $data['tanggal'];

                    $time = strtotime($dateInput);
                    $testDate = date($dateFormat, $time);

                    if ($dateInput == $testDate) {
                        // return 'Valid format' . PHP_EOL;
                        $start = Carbon::parse($data['tanggal'])->format('Y-m-d');
                        $end = Carbon::parse($data['tanggal'])->format('Y-m-d');
                        // return 'Valid format' . $start;
                    } else {
                        // return 'Invalid format' . PHP_EOL;
                        $start = Carbon::parse($data['tanggal'])->startOfMonth()->format('Y-m-d');
                        $end = Carbon::parse($data['tanggal'])->endOfMonth()->format('Y-m-d');
                        // return  'Invalid format' . $start;
                    }

                    $period = CarbonPeriod::create($start, $end);

                    $id_tahun = SettingTahun::where('tahun', Carbon::parse($data['tanggal'])->format('Y'))->select('id')->first();

                    // dd($id_tahun);
                    foreach ($period as $date) {
                        $check = Jadwal::where('id_karyawan', $data['id_karyawan'])->where('tanggal', $date->format('Y-m-d'))->first();

                        if ($check == null) {
                            $jadwal = Jadwal::create([
                                'id_karyawan' => $data['id_karyawan'],
                                'id_tahun' => $id_tahun->id,
                                'tanggal' => $date->format('Y-m-d')
                            ]);
                            $id_jadwal = $jadwal->id;
                        } else {
                            $id_jadwal = $check->id;
                        }

                        $shift = $data['id_shift'];

                        $checkDetail = DetailJadwal::where('id_jadwal', $id_jadwal)->where('id_shift', $shift)->first();
                        if (!$checkDetail) {
                            DetailJadwal::create([
                                'id_jadwal' => $id_jadwal,
                                'id_shift' => $shift,
                            ]);
                        }
                    }
                    // $jadwal = Jadwal::create($data);


                    DB::commit();
                } catch (\Exception $e) {
                    //throw $th;
                    DB::rollBack();
                }
            }
            return response()->json([
                'message' => "$j records successfully uploaded"
            ], Response::HTTP_CREATED);
        } else {
            //no file was uploaded
            throw new \Exception('No file was uploaded', Response::HTTP_BAD_REQUEST);
        }
    }
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

    public function downloadImportJadwal()
    {
        $file = public_path() . "/storage/excel/jadwal.csv";

        $headers = array(
            'Content-Type: text/csv',
        );

        return response()->download($file, 'import-jadwal.csv', $headers);
    }
}
