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
class M_JobController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = DB::table("m_job")->select('id',"job_id_tanos","nama")->orderBy("id","asc")->get();
            return  Datatables::of($query)
                ->addIndexColumn()
                ->make(true);
        }else{
            return view("page.m_job.index");
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("page.m_job.tambah");
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

                $job_id_tanos = $row[0] ?? null; // A
                $nama    = $row[1] ?? null; // B

                if (empty($job_id_tanos)) {
                    throw new \Exception("Row " . ($index+1) . ": job id kosong");
                }
                if (empty($nama)) {
                    throw new \Exception("Row " . ($index+1) . ": nama jabatan kosong");
                }
                

                $exists = DB::table('m_job')->where('job_id_tanos', $job_id_tanos)->first();

                if ($exists) {
                    DB::table('m_job')->where('job_id_tanos', $job_id_tanos)->update([
                        'nama' => $nama,
                    ]);
                } else {
                    DB::table('m_job')->insert([
                        'job_id_tanos' => $job_id_tanos,
                        'nama'    => $nama,
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
