<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProjectsImport;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Validator;
class UsersController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = DB::table('users')->get();
            return  Datatables::of($query)
                ->addIndexColumn()
                ->make(true);
        }else{
            return view("page.users.index");
        }
    }
   
}
