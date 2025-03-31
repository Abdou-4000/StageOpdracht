<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Exports\TeachersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Export all teachers to Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new TeachersExport, 'teachers_full_export.xlsx');
    }

    /**
     * Export all teachers to PDF
     */
    public function exportPDF()
    {
        // Fetch users with city and categories
        $teachers = Teacher::with(['city', 'category'])->get();

        // Load the PDF view and pass the data
        $pdf = Pdf::loadView('exports.teachers', compact('teachers'))->setPaper('a4', 'landscape');

        // Download the generated PDF
        return $pdf->download('teachers_full_export.pdf');
    }
}
