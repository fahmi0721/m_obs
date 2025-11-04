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
class M_ProjectController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $projects = DB::table('m_project')
            ->join('m_entitas', 'm_project.entitas_id', '=', 'm_entitas.id')
            ->select('m_project.*', 'm_entitas.nama')
            ->get();
            return  Datatables::of($projects)
            ->addIndexColumn()
            ->make(true);
        }else{
            return view("page.m_project.index");
        }
    }

     
    /**
     * Show the form for creating a new resource.
     */
    public function download()
    {
        $filePath = public_path('template/project.xlsx');
        $fileName = 'template_project.xlsx';

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
        return view("page.m_project.tambah");
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

                $project_code = $row[0] ?? null; // A
                $deskripsi    = $row[1] ?? null; // B
                $entitas_id   = $row[2] ?? null; // C

                if (empty($project_code)) {
                    throw new \Exception("Row " . ($index+1) . ": project_code kosong");
                }
                if (empty($deskripsi)) {
                    throw new \Exception("Row " . ($index+1) . ": deskripsi kosong");
                }
                if (empty($entitas_id)) {
                    throw new \Exception("Row " . ($index+1) . ": entitas_id kosong");
                }

                $exists = DB::table('m_project')->where('project_code', $project_code)->first();

                if ($exists) {
                    DB::table('m_project')->where('project_code', $project_code)->update([
                        'deskripsi'  => $deskripsi,
                        'entitas_id' => $entitas_id,
                    ]);
                } else {
                    DB::table('m_project')->insert([
                        'project_code' => $project_code,
                        'deskripsi'    => $deskripsi,
                        'entitas_id'   => $entitas_id,
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
