<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProjectsImport;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Validator;
class M_RegionalController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = DB::table("m_regional")->select('id',"regional_id_tanos","nama")->orderBy("id","asc")->get();
            return  Datatables::of($query)
                ->addIndexColumn()
                ->make(true);
        }else{
            return view("page.m_regional.index");
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function download()
    {
        $filePath = public_path('template/regional.xlsx');
        $fileName = 'template_regional.xlsx';

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($filePath, $fileName);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("page.m_regional.tambah");
    }

   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $file = $request->file('file');

        DB::beginTransaction();

        try {
            $importedData = Excel::toArray([], $file)[0]; // Sheet pertama

            $errors = [];

            foreach ($importedData as $index => $row) {
                if ($index < 1) continue; // Lewati row 1 (judul)

                $regional_id_tanos = $row[0] ?? null; // A
                $nama    = $row[1] ?? null; // B
                $latitude    = $row[2] ?? null; // C
                $longitude    = $row[3] ?? null; // D

                if (empty($regional_id_tanos)) {
                    throw new \Exception("Row " . ($index+1) . ": regional id kosong");
                }
                if (empty($nama)) {
                    throw new \Exception("Row " . ($index+1) . ": nama regional");
                }
                if (empty($latitude)) {
                    throw new \Exception("Row " . ($index+1) . ": latitude");
                }
                if (empty($longitude)) {
                    throw new \Exception("Row " . ($index+1) . ": longitude");
                }
                

                $exists = DB::table('m_regional')->where('regional_id_tanos', $regional_id_tanos)->first();

                if ($exists) {
                    DB::table('m_regional')->where('regional_id_tanos', $regional_id_tanos)->update([
                        'nama' => $nama,
                        'latitude'    => $latitude,
                        'longitude'    => $longitude,
                        'updated_at' => now()
                    ]);
                } else {
                    DB::table('m_regional')->insert([
                        'regional_id_tanos' => $regional_id_tanos,
                        'nama'    => $nama,
                        'latitude'    => $latitude,
                        'longitude'    => $longitude,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'errors' => [
                    ['message' => $e->getMessage()]
                ]
            ]);
        }
    }
   
}
