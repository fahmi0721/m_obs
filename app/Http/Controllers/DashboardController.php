<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cek role user (misal field level atau role di tabel user)
        if ($user->level === 'admin') {
            return view('dashboard'); // view untuk admin
        } else {
            $mydata = $this->getMydata();
            $countMyTeam = $this->countMyTeam();
            $my_team = $this->getMyTeam();
            $sop = $this->getSop();
            $videos = $this->getVideo();
            $sop_jabatan = $this->getSopJabatan();
            return view('dashboard_user',compact("mydata","countMyTeam","my_team","sop","videos","sop_jabatan")); // view untuk user biasa
        }
    }

    private function getSopJabatan(){
         $nrp = Auth::user()->username; // anggap kolom username = nrp

        $sop = DB::table('formation as f')
            ->join('m_unit as u', 'u.unit_id_tanos', '=', 'f.unit_id')
            ->join('sop_jabatan as sj', function ($join) {
                $join->on('sj.job_id_tanos', '=', 'f.job_id')
                    ->on('sj.unit_type', '=', 'u.unit_type');
            })
            ->select('sj.*')
            ->where('f.nrp', $nrp)
            ->first();

        return $sop;
    }

    private function getVideo()
    {
        $query = DB::table('video')->where("publish","ya");
        $data = $query->get();
        return $data;
    }

    private function getSop()
    {
        $query = DB::table('sop')->where("status","valid");
        $data = $query->get();
        return $data;
    }

    private function getMyTeam()
    {
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
                'e.umur as umur',
                'e.religion as religion',
                'u.nama as nama_unit',
                'r.nama as nama_regional',
                'f.nama as nama_formation'
            );

        // Jika user bukan admin → tampilkan semua anggota di unit yang sama, kecuali dirinya sendiri
        if ($user->level !== 'admin' && $userFormation) {
            $query->where('f.unit_id', $userFormation->unit_id)
                ->where('f.nrp', '!=', $user->username);
        }

        $data = $query->get();
        return $data;
    }

    private function countMyTeam(){
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

        $data = $query->count();
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
