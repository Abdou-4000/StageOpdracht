<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TeachersExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportExcel()
    {
        return Excel::download(new TeachersExport, 'teachers_full_export.xlsx');
    }
}
