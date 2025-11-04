<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Validator;
class VideoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('video')
                ->select('id', 'judul', 'deskripsi', 'link', 'publish')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('publish', function ($row) {
                    return $row->publish == 'ya'
                        ? '<span class="badge bg-success">Ya</span>'
                        : '<span class="badge bg-danger">Tidak</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<button class="btn btn-info btn-sm btn-view" data-id="'.$row->id.'" data-judul="'.$row->judul.'" data-url="'.$row->link.'" data-bs-toggle="tooltip" title="Lihat Video"><i class="fas fa-play"></i></button>';
                    $btn .= '<a data-bs-toggle="tooltip" title="Edit" href="' . route('video.edit', $row->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                    $btn .= '<button data-bs-toggle="tooltip" title="Hapus" type="button" data-id="' . $row->id . '" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['publish', 'action'])
                ->make(true);
        }else{
            return view("page.video.index");
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function edit($id)
    {
        $video = DB::table('video')->where("id",$id)->first();
        return view("page.video.edit",compact("video"));
    }


     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("page.video.tambah");
    }


    public function store(Request $request)
    {
        $validates 	= [
            "judul"  => "required|max:100",
            "link"  => "required",
            
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
            $data['judul'] = $request->judul;
            $data['deskripsi'] = $request->deskripsi;
            $data['link'] = $request->link;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            $id = DB::table("video")->insert($data);
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
            "judul"  => "required|max:100",
            "link"  => "required",
            
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
           DB::table('video')->where('id', $request->id)->update([
                'judul'       => $request->judul,
                'deskripsi'       => $request->deskripsi,
                'link'     => $request->link,
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
            // Hapus data dari database
            DB::table('video')->where('id', $request->id)->delete();
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
