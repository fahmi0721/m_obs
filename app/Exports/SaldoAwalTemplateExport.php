<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class SaldoAwalTemplateExport implements WithMultipleSheets, WithEvents
{
    protected $akunList;
    protected $entitasList;

    public function __construct()
    {
        // Ambil akun dari view_akun_transaksi_only
        $this->akunList = DB::table('view_akun_transaksi_only')
            ->orderBy('no_akun')
            ->get()
            ->map(function($a){
                return "{$a->id} - {$a->no_akun} - {$a->nama}";
            })->toArray();

        // Ambil entitas dari m_entitas
        $this->entitasList = DB::table('m_entitas')
            ->orderBy('nama')
            ->get()
            ->map(function($e){
                return "{$e->id} - {$e->nama}";
            })->toArray();
    }

    public function sheets(): array
    {
        return [
            // Sheet 1: template saldo
            new class($this->akunList, $this->entitasList) implements \Maatwebsite\Excel\Concerns\FromArray,
                \Maatwebsite\Excel\Concerns\WithTitle {
                private $akunList;
                private $entitasList;

                public function __construct($akunList, $entitasList)
                {
                    $this->akunList = $akunList;
                    $this->entitasList = $entitasList;
                }

                public function array(): array
                {
                    return [
                        ['akun_gl_id', 'tahun', 'entitas_id', 'saldo'],
                        ['', '', '', ''],
                    ];
                }

                public function title(): string
                {
                    return 'template saldo';
                }
            },

            // Sheet 2: data_source hidden
            new class($this->akunList, $this->entitasList) implements \Maatwebsite\Excel\Concerns\FromArray,
                \Maatwebsite\Excel\Concerns\WithTitle {
                private $akunList;
                private $entitasList;

                public function __construct($akunList, $entitasList)
                {
                    $this->akunList = $akunList;
                    $this->entitasList = $entitasList;
                }

                public function array(): array
                {
                        $rows = [];

                        // Tambahkan header di baris 1
                        $rows[] = ['akun_gl', 'entitas'];

                        // Data mulai dari baris 2
                        $max = max(count($this->akunList), count($this->entitasList));
                        for ($i = 0; $i < $max; $i++) {
                            $rows[] = [
                                $this->akunList[$i] ?? null,
                                $this->entitasList[$i] ?? null,
                            ];
                        }

                        return $rows;
                    }

                public function title(): string
                {
                    return 'data_source';
                }
            }
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $spreadsheet = $event->getWriter()->getSpreadsheet();

                $template = $spreadsheet->getSheetByName('template saldo');
                $dataSource = $spreadsheet->getSheetByName('data_source');

                $akunEndRow = max(1, count($this->akunList)) + 1;
                $entitasEndRow = max(1, count($this->entitasList)) + 1;

                $akunRange = "'data_source'!\$A\$2:\$A\$" . $akunEndRow;
                $entitasRange = "'data_source'!\$B\$2:\$B\$" . $entitasEndRow;

                $dataSource->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

                $maxRows = 1000;
                for ($row = 2; $row <= $maxRows; $row++) {
                    // akun_gl_id
                    $validation = $template->getCell('A'.$row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Invalid value');
                    $validation->setError('Value is not in the list.');
                    $validation->setPromptTitle('Select from list');
                    $validation->setPrompt('Please select a value from the drop-down list.');
                    $validation->setFormula1($akunRange);

                    // entitas_id
                    $validationE = $template->getCell('C'.$row)->getDataValidation();
                    $validationE->setType(DataValidation::TYPE_LIST);
                    $validationE->setErrorStyle(DataValidation::STYLE_STOP);
                    $validationE->setAllowBlank(true);
                    $validationE->setShowInputMessage(true);
                    $validationE->setShowErrorMessage(true);
                    $validationE->setShowDropDown(true);
                    $validationE->setErrorTitle('Invalid value');
                    $validationE->setError('Value is not in the list.');
                    $validationE->setPromptTitle('Select from list');
                    $validationE->setPrompt('Please select a value from the drop-down list.');
                    $validationE->setFormula1($entitasRange);
                }

                foreach (['A','B','C','D'] as $col) {
                    $template->getColumnDimension($col)->setAutoSize(true);
                }

                $template->getStyle('A1:D1')->getFont()->setBold(true);
            }
        ];
    }
}
