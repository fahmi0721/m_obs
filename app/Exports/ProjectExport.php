<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ProjectExport implements
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    ShouldAutoSize
{
    protected $data;

    public function __construct()
    {
        // Ambil akun dari view_akun_transaksi_only
        $this->data = DB::table('m_project')
            ->join('m_entitas', 'm_project.entitas_id', '=', 'm_entitas.id')
            ->select('m_project.project_code','m_project.deskripsi', 'm_entitas.nama')
            ->get();

    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Project Code', 'Deskripsi', 'Entitas'];
    }

    public function styles(Worksheet $sheet)
    {
        // Style heading (baris 1)
        $sheet->getStyle('A1:C1')->applyFromArray([
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
        $sheet->getStyle('A2:C100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        return [];
    }

   

    
}
