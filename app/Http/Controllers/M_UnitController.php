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
class M_UnitController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = DB::table("m_unit")->select('id',"unit_id_tanos","nama","unit_type")->orderBy("id","asc")->get();
            return  Datatables::of($query)
                ->addIndexColumn()
                ->make(true);
        }else{
            return view("page.m_unit.index");
        }
    }

    public function select(Request $request)
    {
        $query = $request->get('q');
        $m_akun = DB::table("m_unit")
        ->select('unit_type')
        ->distinct()
        ->where('unit_type', '!=', '')
        ->where('unit_type', 'like', '%' . $query . '%')
        ->orderBy('unit_type', 'asc')
        ->get();
        return response()->json($m_akun);
    }


    
    /**
     * Show the form for creating a new resource.
     */
    public function download()
    {
        $filePath = public_path('template/unit.xlsx');
        $fileName = 'template_unit.xlsx';

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
        return view("page.m_unit.tambah");
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

                $unit_id_tanos = $row[0] ?? null; // A
                $nama    = $row[1] ?? null; // B
                $unit_type    = $row[2] ?? null; // B

                if (empty($unit_id_tanos)) {
                    throw new \Exception("Row " . ($index+1) . ": unit id kosong");
                }
                if (empty($nama)) {
                    throw new \Exception("Row " . ($index+1) . ": nama unit kosong");
                }

                  if (empty($unit_type)) {
                    throw new \Exception("Row " . ($index+1) . ": unit tipe kosong");
                }
                

                $exists = DB::table('m_unit')->where('unit_id_tanos', $unit_id_tanos)->first();

                if ($exists) {
                    DB::table('m_unit')->where('unit_id_tanos', $unit_id_tanos)->update([
                        'nama' => $nama,
                        'unit_type' => $unit_type,
                        'updated_at' => now()
                    ]);
                } else {
                    DB::table('m_unit')->insert([
                        'unit_id_tanos' => $unit_id_tanos,
                        'nama'    => $nama,
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
