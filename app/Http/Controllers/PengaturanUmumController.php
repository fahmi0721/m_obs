<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use Image;
class PengaturanUmumController extends Controller
{
    protected  $path;
    
    public function __construct()
    {
        $this->path = base_path('../../public_html/tes/uploads/images/');
        // $this->path = public_path(config('custom.upload_images'));
    }
    public function index()
    {
        return view('page.pengaturan');
    }
    


     public function store(Request $request)
    {
        $validates  = [
            "nama_aplikasi"  => "required",
        ];
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $validates += ["logo" => 'image|mimes:jpeg,png,jpg|max:2048'];
        }

        if ($request->hasFile('favicon') && $request->file('favicon')->isValid()) {
            $validates += ["favicon" => 'image|mimes:jpeg,png,jpg|max:2048'];
        }
        $validation = Validator::make($request->all(), $validates);
        if($validation->fails()) {
            return response()->json([
                "status"    => "error",
                "messages"   => $validation->errors()->first()
            ], 401);
        }

        DB::beginTransaction();
        try {
            /** Create Objek Data */
            $data = [
                'nama_aplikasi' => $request->nama_aplikasi,
                'created_at'=> Carbon::now(),
            ];

            /** Upload File Logo */
            $logo = $request->file('logo');
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $logoname = uniqid() . '.' . $logo->getClientOriginalExtension();
                $logo->move($this->path,$logoname);
                $data += ['logo' => $logoname];
            }

            /** Upload File Favicon */
            $favicon = $request->file('favicon');
            if ($request->hasFile('favicon') && $request->file('favicon')->isValid()) {
                $faviconname = uniqid() . '.' . $favicon->getClientOriginalExtension();
                $favicon->move($this->path,$faviconname);
                $data += ['favicon' => $faviconname];
            }
            DB::table('base_sistem')->update($data);
            DB::commit();
            return response()->json([
                'status'   => 'success',
                'messages' => 'Data berhasil tersimpan',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'   => 'error',
                'messages' => $e->getMessage()
            ], 500);
        }
        
    }
}
