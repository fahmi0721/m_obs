<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class FormationExport implements
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    ShouldAutoSize
{
    protected $data;

    public function __construct()
    {
        // Ambil akun dari view_akun_transaksi_only
        $this->data = $query = DB::table('formation as f')
                ->join('m_job as j', 'f.job_id', '=', 'j.job_id_tanos')
                ->join('m_regional as r', 'f.regional_id', '=', 'r.regional_id_tanos')
                ->join('m_unit as u', 'f.unit_id', '=', 'u.unit_id_tanos')
                ->join('employee as em', 'f.nrp', '=', 'em.nrp')
                ->join('m_project as p', 'f.project_code', '=', 'p.project_code')
                ->join('m_entitas as e', 'p.entitas_id', '=', 'e.id')
                ->select(
                    'f.formation_id_tanos',
                    'em.nrp',
                    'em.nama',
                    'f.project_code as project',
                    'f.nama as formation_name',
                    DB::raw("CONCAT(e.nama, ' / ', r.nama, ' / ', u.nama) as entitas_regional_unit")
                )
                ->get();

    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['ID Formasi', 'NRP', 'Nama', 'Project Code','Formation','Unit'];
    }

    public function styles(Worksheet $sheet)
    {
        // Style heading (baris 1)
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        // Style isi tabel
        $sheet->getStyle('A2:F100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        return [];
    }

   

    
}
