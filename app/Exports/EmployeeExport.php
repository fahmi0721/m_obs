<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class EmployeeExport implements
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    ShouldAutoSize
{
    protected $data;

    public function __construct()
    {
        // Ambil akun dari view_akun_transaksi_only
        $this->data = DB::table('employee')->select('employee_id_tanos','nrp','nama','alamat','umur','email','gender','status','religion')->get();

    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Employee ID Tanos', 'NRP', 'Nama', 'Alamat', 'Usia', 'Email', 'Gender', 'Status', 'Agama'];
    }

    public function styles(Worksheet $sheet)
    {
        // Style heading (baris 1)
        $sheet->getStyle('A1:I1')->applyFromArray([
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
        $sheet->getStyle('A2:I100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        return [];
    }

   

    
}
