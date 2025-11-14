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
            $dashboard = DB::table('formation as f')
            ->selectRaw("
                COUNT(DISTINCT f.nrp) as total_employee,
                COUNT(DISTINCT u.id) as total_unit,
                COUNT(DISTINCT j.id) as total_job,
                COUNT(DISTINCT e.id) as total_entitas
            ")
            ->join('employee as emp', 'emp.nrp', '=', 'f.nrp')
            ->join('m_unit as u', 'u.unit_id_tanos', '=', 'f.unit_id')
            ->join('m_job as j', 'j.job_id_tanos', '=', 'f.job_id')
            ->join('m_project as p', 'p.project_code', '=', 'f.project_code')
            ->join('m_entitas as e', 'e.id', '=', 'p.entitas_id')
            ->first();
            $regional = DB::table('m_regional')->orderBy('nama')->get();
            $entitas  = DB::table('m_entitas')->orderBy('nama')->get();

            return view('dashboard',compact("dashboard","regional","entitas")); // view untuk admin
        } else {
            $mydata = $this->getMydata();
            $countMyTeam = $this->countMyTeam();
            $my_team = $this->getMyTeam();
            $sop = $this->getSop();
            $videos = $this->getVideo();
            $sop_jabatan = $this->getSopJabatan();
            $aturan = $this->getAtiranLarangan();
            $edarans = $this->getEdaran();
            return view('dashboard_user',compact("mydata","countMyTeam","my_team","sop","videos","sop_jabatan","aturan","edarans")); // view untuk user biasa
        }
    }

    public function nrp(Request $request){
        $query = $request->get('q');
        $data = DB::table("employee")
            ->where(function($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%")
                ->orWhere('nrp', 'like', "%{$query}%");
            })
            ->orderBy('nama', 'asc')
            ->get();
        return response()->json($data);
    }

    public function regional(Request $request){
        $query = $request->get('q');
        $data = DB::table("m_regional")->where('nama','like','%'.$query.'%')->orderBy('nama','asc')->get();
        return response()->json($data);
    }

    public function entitas(Request $request){
        $query = $request->get('q');
        $m_akun = DB::table("m_entitas")->where('nama','like','%'.$query.'%')->orderBy('nama','asc')->get();
        return response()->json($m_akun);
    }

    public function mapData(Request $request)
    {
        $entitas  = $request->entitas;
        $regional = $request->regional;
        $nrp      = $request->nrp; // filter NRP

        $query = DB::table('formation as f')
            ->join('m_regional as r', 'r.regional_id_tanos', '=', 'f.regional_id')
            ->join('m_unit as u', 'u.unit_id_tanos', '=', 'f.unit_id')
            ->join('employee as e', 'e.nrp', '=', 'f.nrp')
            ->join('m_job as j', 'j.job_id_tanos', '=', 'f.job_id')
            ->join('m_project as p', 'p.project_code', '=', 'f.project_code')
            ->join('m_entitas as t', 't.id', '=', 'p.entitas_id')
            ->select(
                'r.regional_id_tanos','r.nama as regional','r.latitude','r.longitude',
                'u.unit_id_tanos','u.nama as unit','u.unit_type',
                'e.nrp','e.nama as employee',
                'j.nama as job',
                't.id as entitas_id','t.nama as entitas'
            );

        if ($entitas)  $query->where('t.id', $entitas);
        if ($regional) $query->where('r.regional_id_tanos', $regional);
        if ($nrp)      $query->where('e.nrp', 'like', "%$nrp%"); // filter NRP

        $data = $query->get()->groupBy('regional_id_tanos');

        $result = [];
        foreach ($data as $regional_id => $items) {
            $first = $items[0];

            $units = [];
            foreach ($items->groupBy('unit_id_tanos') as $unit_id => $u) {
                $units[] = [
                    'unit_id'   => $unit_id,
                    'unit'      => $u[0]->unit,
                    'unit_type' => $u[0]->unit_type,
                    'crew'      => $u->map(fn($x) => [
                        'nrp' => $x->nrp,
                        'nama' => $x->employee,
                        'job'  => $x->job
                    ])
                ];
            }

            $result[] = [
                'regional_id' => $regional_id,
                'regional'    => $first->regional,
                'lat'         => $first->latitude,
                'lng'         => $first->longitude,
                'units'       => $units
            ];
        }

        return response()->json($result);
    }


    private function getEdaran(){
         $nrp = Auth::user()->username; // anggap kolom username = nrp

        $query = DB::table('surat_edaran');
        $data = $query->get();
        return $data;
    }


    private function getAtiranLarangan(){
         $nrp = Auth::user()->username; // anggap kolom username = nrp

        $query = DB::table('aturan_larangan');
        $data = $query->get();
        return $data;
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
