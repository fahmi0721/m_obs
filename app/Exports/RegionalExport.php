<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class RegionalExport implements
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    ShouldAutoSize
{
    protected $data;

    public function __construct()
    {
        // Ambil akun dari view_akun_transaksi_only
        $this->data = DB::table("m_regional")->select("regional_id_tanos","nama")->orderBy("id","asc")->get();

    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Regional ID Tanos', 'Nama'];
    }

    public function styles(Worksheet $sheet)
    {
        // Style heading (baris 1)
        $sheet->getStyle('A1:B1')->applyFromArray([
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
        $sheet->getStyle('A2:B100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        return [];
    }

   

    
}
