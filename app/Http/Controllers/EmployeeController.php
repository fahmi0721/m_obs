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
class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = DB::table('employee')->get();
            return  Datatables::of($query)
                ->addIndexColumn()
                ->make(true);
        }else{
            return view("page.employee.index");
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function download()
    {
        $filePath = public_path('template/employee.xlsx');
        $fileName = 'template_employee.xlsx';

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
        return view("page.employee.tambah");
    }

   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $file = $request->file('file');

        DB::beginTransaction();

        try {
            DB::table('employee')->delete();
            $importedData = Excel::toArray([], $file)[0]; // Sheet pertama

            foreach ($importedData as $index => $row) {
                if ($index < 1) continue; // Lewati row header

                $employee_id_tanos = $row[0] ?? null; // A
                $nrp      = $row[1] ?? null; // B
                $nama     = $row[2] ?? null; // C
                $alamat   = $row[3] ?? null; // D
                $umur     = $row[4] ?? null; // E
                $email    = $row[5] ?? null; // F
                $gender   = $row[6] ?? null; // G
                $status   = $row[7] ?? null; // H
                $religion = $row[8] ?? null; // I

                // --- Validasi dasar
                if (empty($employee_id_tanos)) throw new \Exception("Row " . ($index+1) . ": employee id kosong");
                if (empty($nrp)) throw new \Exception("Row " . ($index+1) . ": nrp kosong");
                if (empty($nama)) throw new \Exception("Row " . ($index+1) . ": nama kosong");
                if (empty($umur)) throw new \Exception("Row " . ($index+1) . ": umur kosong");
                if (empty($gender)) throw new \Exception("Row " . ($index+1) . ": gender kosong");
                if (empty($status)) throw new \Exception("Row " . ($index+1) . ": status kosong");
                if (empty($religion)) throw new \Exception("Row " . ($index+1) . ": religion kosong");

                // --- Cek apakah employee sudah ada
                $exists = DB::table('employee')->where('employee_id_tanos', $employee_id_tanos)->first();

                if ($exists) {
                    // Update jika sudah ada
                    DB::table('employee')->where('employee_id_tanos', $employee_id_tanos)->update([
                        'nrp' => $nrp,
                        'nama' => $nama,
                        'alamat' => $alamat,
                        'email' => $email,
                        'umur' => $umur,
                        'gender' => $gender,
                        'status' => $status,
                        'religion' => $religion,
                    ]);
                } else {
                    // Insert employee baru
                    DB::table('employee')->insert([
                        'employee_id_tanos' => $employee_id_tanos,
                        'nrp' => $nrp,
                        'nama' => $nama,
                        'alamat' => $alamat,
                        'email' => $email,
                        'umur' => $umur,
                        'gender' => $gender,
                        'status' => $status,
                        'religion' => $religion,
                    ]);

                    // === AUTO INSERT USER ===
                    // cek apakah user sudah ada berdasarkan nrp (username)
                    $userExists = DB::table('users')
                        ->where('username', $nrp)
                        ->exists();

                    if (!$userExists) {
                        DB::table('users')->insert([
                            'nama' => $nama,
                            'username' => $nrp,
                            'email' => $email,
                            'level' => 'crew',
                            'password' => Hash::make('Password123'), // gunakan hash untuk keamanan
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
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
