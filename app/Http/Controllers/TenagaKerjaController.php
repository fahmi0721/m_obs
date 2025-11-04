<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class TenagaKerjaController extends Controller
{
    public function index(){
       
        $data = $this->getMydata();
        $team = $this->getMyTeam();
        return response()->json(array("mydata" => $data, "team" => $team));
    }

    public function getRegional(){
       $userNrp = Auth::user()->username;

        // 1️⃣ Ambil semua entitas user login
        $idEntitas = DB::table('formation as f')
            ->join('m_project as p', 'p.project_code', '=', 'f.project_code')
            ->where('f.nrp', $userNrp)
            ->pluck('p.entitas_id');

        // 2️⃣ Ambil semua regional_id_tanos yang dimiliki user login
        $regionalUser = DB::table('formation as f')
            ->join('m_project as p', 'p.project_code', '=', 'f.project_code')
            ->where('f.nrp', $userNrp)
            ->pluck('f.regional_id');

        // 3️⃣ Ambil semua regional dengan entitas sama
        $data = DB::table('m_regional as r')
            ->join('formation as f', 'f.regional_id', '=', 'r.regional_id_tanos')
            ->join('m_project as p', 'p.project_code', '=', 'f.project_code')
            ->select(
                'r.id',
                'r.nama',
                'r.latitude',
                'r.longitude',
                'r.regional_id_tanos'
            )
            ->whereIn('p.entitas_id', $idEntitas)
            ->distinct()
            ->get();

        // 4️⃣ Tandai regional milik user login
        $data = $data->map(function ($item) use ($regionalUser) {
            $item->is_login_user = $regionalUser->contains($item->regional_id_tanos);
            return $item;
        });
        return response()->json($data);
    }

    private function getMyTeam(){
        $user = Auth::user();
        // Ambil data formasi utama user (untuk tahu unit-nya)
        $userFormation = DB::table('formation')
            ->where('nrp', $user->username)
            ->first();

        $query = DB::table('formation as f')
            ->join('m_unit as u', 'f.unit_id', '=', 'u.unit_id_tanos')
            ->join('m_regional as r', 'f.regional_id', '=', 'r.regional_id_tanos')
            ->join('employee as e', 'f.nrp', '=', 'e.nrp')
            ->select(
                'f.nrp',
                'e.nama as nama_karyawan',
                'u.nama as nama_unit',
                'r.nama as nama_regional',
                'f.nama as nama_formation'
            );

        // Jika user bukan admin → tampilkan semua yang satu unit
        if ($user->level !== 'admin' && $userFormation) {
            $query->where('f.unit_id', $userFormation->unit_id);
        }

        $data = $query->get();
        return $data;
    }

    private function getMydata(){
         $user = Auth::user();

            $query = DB::table('formation as f')
                ->join('m_unit as u', 'f.unit_id', '=', 'u.unit_id_tanos')
                ->join('m_regional as r', 'f.regional_id', '=', 'r.regional_id_tanos')
                ->join('employee as e', 'f.nrp', '=', 'e.nrp')
                ->select(
                    'f.nrp',
                    'e.nama as nama_karyawan',
                    'u.nama as nama_unit',
                    'r.nama as nama_regional',
                    'f.nama as nama_formation'
                );

            // Jika user bukan admin → filter berdasarkan nrp
            if ($user->level !== 'admin') {
                $query->where('f.nrp', $user->username);
            }

            $data = $query->first();
            return $data;
    }
}
