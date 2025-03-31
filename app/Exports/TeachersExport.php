<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TeachersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return Teacher::with(['city', 'category'])->get();
    }

    public function headings(): array {
        return ["ID", "Name", "", "Email", "Phone", "Company", "", "Address", "", "", "Categories"];
    }

    public function map($teacher): array {
        return [
            $teacher->id,
            $teacher->firstname,
            $teacher->lastname,
            $teacher->email,
            $teacher->phone,
            $teacher->companyname,
            $teacher->companynumber,
            $teacher->street,
            $teacher->streetnumber,
            $teacher->city ? $teacher->city->name : 'N/A',
            $teacher->category->pluck('name')->implode(', '),
        ];
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Merge cells for multi-row headings
                $event->sheet->mergeCells('B1:C1');
                $event->sheet->mergeCells('F1:G1'); 
                $event->sheet->mergeCells('H1:J1'); 

                // Style the merged cells
                $event->sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
