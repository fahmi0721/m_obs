<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Validator;

class EdaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('surat_edaran')
                ->select('id', 'no_surat', 'perihal', 'tanggal_surat', 'file')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                // 🔸 Format tanggal ke tampilan Indonesia
                ->editColumn('tanggal_surat', function ($row) {
                    return Carbon::parse($row->tanggal_surat)->translatedFormat('d F Y');
                })

                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a data-bs-toggle="tooltip" title="Download" href="' . asset('uploads/edaran/' . $row->file) . '" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf"></i></a>';
                    $btn .= '<a data-bs-toggle="tooltip" title="Edit" href="' . route('edaran.edit', $row->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                    $btn .= '<button data-bs-toggle="tooltip" title="Hapus" type="button" data-id="' . $row->id . '" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['tanggal_surat', 'action'])
                ->make(true);
        }else{
            return view("page.edaran.index");
        }
    }


     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("page.edaran.tambah");
    }

    public function edit($id)
    {
        // Ambil data SOP berdasarkan id
        $sop = DB::table('surat_edaran')->where('id', $id)->first();

        // Jika data tidak ditemukan
        if (!$sop) {
            return redirect()->route('edaran')->with('error', 'Data tidak ditemukan.');
        }

        // Kirim data ke view edit
        return view('page.edaran.edit', compact('sop'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validates 	= [
            "no_surat"  => "required",
            "perihal"  => "required",
            "tgl_surat"  => "required",
            "file"  => "required|mimes:pdf|max:2048",
            
        ];

        $msg = [
            "no_surat.required" => "No Surat Wajib Diisi!",
            "perihal.required" => "Perihal Wajib Diisi!",
            "tgl_surat.required" => "Tanggal Wajib Diisi!",
            "file.required" => "File Wajib Diisi!",
            "file.mimes" => "Dokumen File Wajib PDF!",
            "file.max" => "Maksimal File 2mb!",
        ];

        $validation = Validator::make($request->all(), $validates,$msg);
        if($validation->fails()) {
            return response()->json([
                "status"    => "warning",
                "messages"   => $validation->errors()->first()
            ], 401);
        }
        DB::beginTransaction();
        try {
             // ✅ Siapkan folder tujuan upload
            // $uploadPath = base_path('../../public_html/tes/uploads/edaran/');
            $uploadPath = public_path('uploads/edaran/');
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
            $data['no_surat'] = $request->no_surat;
            $data['perihal'] = $request->perihal;
            $data['tanggal_surat'] = $request->tgl_surat;
            $data['file'] = $fileName;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            $id = DB::table("surat_edaran")->insert($data);
            DB::commit();
            return response()->json(['status'=>'success', 'messages'=>"Data berhasil disimpan."], 200);
        } catch(QueryException $e) { 
            DB::rollback();
            return response()->json(['status'=>'error','messages'=> $e->errorInfo ], 500);
        }
    }


    public function update(Request $request)
    {
        $validates = [
            "no_surat"  => "required",
            "perihal"  => "required",
            "tgl_surat"  => "required",
        ];
        $msg = [
            "no_surat.required" => "No Surat Wajib Diisi!",
            "perihal.required" => "Perihal Wajib Diisi!",
            "tgl_surat.required" => "Tanggal Wajib Diisi!",
        ];

        if ($request->hasFile('file')) {
            $rules['file'] = 'mimes:pdf|max:2048';
            $msg['file.mimes'] = "File Wajib Diisi!";
            $msg['file.max'] = "Maksimal File 2mb!";
        }

        $validation = Validator::make($request->all(), $validates);
        if($validation->fails()) {
            return response()->json([
                "status"    => "warning",
                "messages"   => $validation->errors()->first()
            ], 401);
        }

        DB::beginTransaction();
        try {
            $sop = DB::table('surat_edaran')->where('id', $request->id)->first();
            if (!$sop) {
                return response()->json(['status'=>'warning', 'messages'=>'Data tidak ditemukan.'], 404);
            }

            $uploadPath = public_path('uploads/edaran/');
            // $uploadPath = base_path('../../public_html/tes/uploads/sop/');
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

            DB::table('surat_edaran')->where('id', $request->id)->update([
                'no_surat'       => $request->no_surat,
                'perihal'       => $request->perihal,
                'tanggal_surat'     => $request->tgl_surat,
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
            $sop = DB::table('surat_edaran')->where('id', $request->id)->first();

            if (!$sop) {
                return response()->json([
                    'status' => 'warning',
                    'messages' => 'Data tidak ditemukan.'
                ], 404);
            }

            // Hapus file dari folder jika ada
            if ($sop->file) {
                // $filePath = base_path('../../public_html/tes/uploads/edaran/'.$sop->file);
                $filePath = public_path('uploads/edaran/' . $sop->file);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Hapus data dari database
            DB::table('surat_edaran')->where('id', $request->id)->delete();

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
