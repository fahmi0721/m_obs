<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProjectsImport  implements WithHeadingRow
{
   // kosong — kita hanya butuh WithHeadingRow agar hasilnya menjadi koleksi dengan header
}
