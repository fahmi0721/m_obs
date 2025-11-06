<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Validator;

class SopJabatanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
           $data = DB::table('sop_jabatan')
            ->join('m_job', 'sop_jabatan.job_id_tanos', '=', 'm_job.job_id_tanos')
            ->select(
                'sop_jabatan.id',
                'sop_jabatan.nama as nama_sop',
                'sop_jabatan.job_id_tanos',
                'sop_jabatan.unit_type',
                'sop_jabatan.file',
                'm_job.nama as nama_job'
            )
            ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a data-bs-toggle="tooltip" title="Download" href="' . asset('uploads/sop_jabatan/' . $row->file) . '" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i></a>';
                    $btn .= '<a data-bs-toggle="tooltip" title="Edit" href="' . route('sop_jabatan.edit', $row->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                    $btn .= '<button data-bs-toggle="tooltip" title="Hapus" type="button" data-id="' . $row->id . '" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'file', 'action'])
                ->make(true);
        }else{
            return view("page.sop_jabatan.index");
        }
    }


     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("page.sop_jabatan.tambah");
    }

    public function edit($id)
    {
        // Ambil data SOP berdasarkan id
        $data = DB::table('sop_jabatan')->where('id', $id)->first();

        // Jika data tidak ditemukan
        if (!$data) {
            return redirect()->route('sop_jabatan')->with('error', 'Data tidak ditemukan.');
        }
        $jabatan = DB::table('m_job')->where("job_id_tanos",$data->job_id_tanos)->first();
        $unit_type = DB::table('m_unit')->where("unit_type",$data->unit_type)->first();
// dd($unit_type);
        // Kirim data ke view edit
       return view('page.sop_jabatan.edit', compact('data','id','jabatan','unit_type'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validates 	= [
            "nama"  => "required",
            "unit_type"  => "required",
            "job_id_tanos"  => "required",
            "file"  => "required|mimes:pdf|max:2048",
            
        ];

        $messages = [
            "nama.required" => "Nama Wajib Diisi.",
            "unit_type.required" => "Tipe Unit Wajib Diisi.",
            "job_id_tanos.required" => "Jabatan  Wajib Diisi.",
            "file.required" => "File Wajib Diisi."
        ];

        $validation = Validator::make($request->all(), $validates,$messages);
        if($validation->fails()) {
            return response()->json([
                "status"    => "warning",
                "messages"   => $validation->errors()->first()
            ], 401);
        }
        DB::beginTransaction();
        try {
             // ✅ Siapkan folder tujuan upload
            $uploadPath = public_path('uploads/sop_jabatan/');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // ✅ Proses upload file PDF
            $fileName = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                // Buat nama unik untuk file, misalnya berdasarkan timestamp
                $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move($uploadPath, $fileName);
            }
            $data['nama'] = $request->nama;
            $data['unit_type'] = $request->unit_type;
            $data['job_id_tanos'] = $request->job_id_tanos;
            $data['file'] = $fileName;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            $id = DB::table("sop_jabatan")->insert($data);
            DB::commit();
            return response()->json(['status'=>'success', 'messages'=>"Data berhasil disimpan."], 200);
        } catch(QueryException $e) { 
            DB::rollback();
            return response()->json(['status'=>'error','messages'=> $e->errorInfo ], 500);
        }
    }


    public function update(Request $request)
    {
       $validates 	= [
            "nama"  => "required",
            "unit_type"  => "required",
            "job_id_tanos"  => "required",
            
        ];

        $messages = [
            "nama.required" => "Nama Wajib Diisi.",
            "unit_type.required" => "Tipe Unit Wajib Diisi.",
            "job_id_tanos.required" => "Jabatan  Wajib Diisi.",
        ];

        if ($request->hasFile('file')) {
            $validates['file'] = 'mimes:pdf|max:2048';
            $messages["file.required"] = "File Wajib Diisi.";
        }

        $validation = Validator::make($request->all(), $validates,$messages);
        if($validation->fails()) {
            return response()->json([
                "status"    => "warning",
                "messages"   => $validation->errors()->first()
            ], 401);
        }

        DB::beginTransaction();
        try {
            $sop = DB::table('sop_jabatan')->where('id', $request->id)->first();
            if (!$sop) {
                return response()->json(['status'=>'warning', 'messages'=>'Data tidak ditemukan.'], 404);
            }

            $uploadPath = public_path('uploads/sop_jabatan/');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);

            $fileName = $sop->file;
            if ($request->hasFile('file')) {
                if ($sop->file && file_exists($uploadPath.$sop->file)) {
                    @unlink($uploadPath.$sop->file);
                }

                $file = $request->file('file');
                $fileName = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move($uploadPath, $fileName);
            }

            DB::table('sop_jabatan')->where('id', $request->id)->update([
                'nama'       => $request->nama,
                'unit_type'     => $request->unit_type,
                'job_id_tanos'  => $request->job_id_tanos,
                'file'       => $fileName,
                'updated_at' => now(),
            ]);

            DB::commit();
            return response()->json(['status'=>'success', 'messages'=>'Data berhasil diupdate.'], 200);

        } catch (QueryException $e) {
            DB::rollback();
            return response()->json(['status'=>'error', 'messages'=>$e->getMessage()], 500);
        }
    }





    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            // Ambil data berdasarkan id
            $sop = DB::table('sop_jabatan')->where('id', $request->id)->first();

            if (!$sop) {
                return response()->json([
                    'status' => 'warning',
                    'messages' => 'Data tidak ditemukan.'
                ], 404);
            }

            // Hapus file dari folder jika ada
            if ($sop->file) {
                $filePath = public_path('uploads/sop_jabatan/' . $sop->file);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Hapus data dari database
            DB::table('sop_jabatan')->where('id', $request->id)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'messages' => 'Data berhasil dihapus.'
            ], 200);

        } catch (QueryException $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'messages' => $e->getMessage()
            ], 500);
        }
    }
}
