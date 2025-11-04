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
class FormationController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = DB::table('formation as f')
                ->join('m_job as j', 'f.job_id', '=', 'j.job_id_tanos')
                ->join('m_regional as r', 'f.regional_id', '=', 'r.regional_id_tanos')
                ->join('m_unit as u', 'f.unit_id', '=', 'u.unit_id_tanos')
                ->join('employee as em', 'f.nrp', '=', 'em.nrp')
                ->join('m_project as p', 'f.project_code', '=', 'p.project_code')
                ->join('m_entitas as e', 'p.entitas_id', '=', 'e.id')
                ->select(
                    'f.formation_id_tanos',
                    'f.project_code as project',
                    'f.nama as formation_name',
                    DB::raw("CONCAT(em.nrp, ' - ',em.nama) as employee"),
                    DB::raw("CONCAT(e.nama, ' / ', r.nama, ' / ', u.nama) as entitas_regional_unit")
                )
                ->get();
            return  Datatables::of($query)
                ->addIndexColumn()
                ->make(true);
        }else{
            return view("page.formation.index");
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function download()
    {
        $filePath = public_path('template/formation.xlsx');
        $fileName = 'template_formation.xlsx';

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
        return view("page.formation.tambah");
    }

   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $file = $request->file('file');

        DB::beginTransaction();

        try {
            DB::table('formation')->delete();
            $importedData = Excel::toArray([], $file)[0]; // Sheet pertama

            $errors = [];

            foreach ($importedData as $index => $row) {
                if ($index < 1) continue; // Lewati row 1 (judul)

                $formation_id_tanos = $row[0] ?? null; // A
                $nama    = $row[1] ?? null; // B
                $project_code    = $row[2] ?? null; // C
                $unit_id    = $row[3] ?? null; // D
                $job_id    = $row[4] ?? null; // E
                $regional_id    = $row[5] ?? null; // F
                $nrp    = $row[6] ?? null; // G

                if (empty($formation_id_tanos)) {
                    throw new \Exception("Row " . ($index+1) . ": formation id kosong");
                }
                if (empty($nama)) {
                    throw new \Exception("Row " . ($index+1) . ": nama formation kosong");
                }
                if (empty($project_code)) {
                    throw new \Exception("Row " . ($index+1) . ": project code kosong");
                }
                if (empty($unit_id)) {
                    throw new \Exception("Row " . ($index+1) . ": unit id kosong");
                }
                if (empty($job_id)) {
                    throw new \Exception("Row " . ($index+1) . ": job id kosong");
                }
                if (empty($regional_id)) {
                    throw new \Exception("Row " . ($index+1) . ": regional id kosong");
                }
                if (empty($nrp)) {
                    throw new \Exception("Row " . ($index+1) . ": NRP id kosong");
                }
                

                

                $exists = DB::table('formation')->where('formation_id_tanos', $formation_id_tanos)->first();

                if ($exists) {
                    DB::table('formation')->where('formation_id_tanos', $formation_id_tanos)->update([
                        'nama' => $nama,
                        'project_code' => $project_code,
                        'unit_id' => $unit_id,
                        'job_id' => $job_id,
                        'regional_id' => $regional_id,
                        'nrp' => $nrp,
                    ]);
                } else {
                    DB::table('formation')->insert([
                        'formation_id_tanos' => $formation_id_tanos,
                        'nama' => $nama,
                        'project_code' => $project_code,
                        'unit_id' => $unit_id,
                        'job_id' => $job_id,
                        'regional_id' => $regional_id,
                        'nrp' => $nrp,
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
