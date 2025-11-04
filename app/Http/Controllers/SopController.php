<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Validator;

class SopController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('sop')
                ->select('id', 'kode', 'nama', 'status', 'deskripsi', 'file')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    return $row->status == 'valid'
                        ? '<span class="badge bg-success">Valid</span>'
                        : '<span class="badge bg-danger">Invalid</span>';
                })
                ->editColumn('file', function ($row) {
                    if ($row->file) {
                        return '<a href="' . asset('template/sop/' . $row->file) . '" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-download"></i> Lihat
                                </a>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a data-bs-toggle="tooltip" title="Download" href="' . asset('uploads/sop/' . $row->file) . '" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i></a>';
                    $btn .= '<a data-bs-toggle="tooltip" title="Edit" href="' . route('sop.edit', $row->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                    $btn .= '<button data-bs-toggle="tooltip" title="Hapus" type="button" data-id="' . $row->id . '" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'file', 'action'])
                ->make(true);
        }else{
            return view("page.sop.index");
        }
    }


     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("page.sop.tambah");
    }

    public function edit($id)
    {
        // Ambil data SOP berdasarkan id
        $sop = DB::table('sop')->where('id', $id)->first();

        // Jika data tidak ditemukan
        if (!$sop) {
            return redirect()->route('sop')->with('error', 'Data tidak ditemukan.');
        }

        // Kirim data ke view edit
        return view('page.sop.edit', compact('sop'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validates 	= [
            "kode"  => "required",
            "nama"  => "required",
            "status"  => "required",
            "file"  => "required|mimes:pdf|max:2048",
            
        ];

        $validation = Validator::make($request->all(), $validates);
        if($validation->fails()) {
            return response()->json([
                "status"    => "warning",
                "messages"   => $validation->errors()->first()
            ], 401);
        }
        DB::beginTransaction();
        try {
             // ✅ Siapkan folder tujuan upload
            $uploadPath = public_path('uploads/sop/');
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
            $data['kode'] = $request->kode;
            $data['nama'] = $request->nama;
            $data['status'] = $request->status;
            $data['deskripsi'] = $request->deskripsi;
            $data['file'] = $fileName;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            $id = DB::table("sop")->insert($data);
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
            "kode"   => "required",
            "nama"   => "required",
            "status" => "required",
        ];

        if ($request->hasFile('file')) {
            $rules['file'] = 'mimes:pdf|max:2048';
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
            $sop = DB::table('sop')->where('id', $request->id)->first();
            if (!$sop) {
                return response()->json(['status'=>'warning', 'messages'=>'Data tidak ditemukan.'], 404);
            }

            $uploadPath = public_path('uploads/sop/');
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

            DB::table('sop')->where('id', $request->id)->update([
                'kode'       => $request->kode,
                'nama'       => $request->nama,
                'status'     => $request->status,
                'deskripsi'  => $request->deskripsi,
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
            $sop = DB::table('sop')->where('id', $request->id)->first();

            if (!$sop) {
                return response()->json([
                    'status' => 'warning',
                    'messages' => 'Data tidak ditemukan.'
                ], 404);
            }

            // Hapus file dari folder jika ada
            if ($sop->file) {
                $filePath = public_path('template/sop/' . $sop->file);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Hapus data dari database
            DB::table('sop')->where('id', $request->id)->delete();

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
