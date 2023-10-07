<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StockExport implements FromView, WithColumnWidths, WithEvents
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('export.stock', [
            'data' => $this->data['data']
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 35,            
            'C' => 10,            
            'D' => 15,            
            'E' => 25,            
            'F' => 25
        ];
    }

    public function registerEvents(): array 
    {
        $totalRows = 0;
        return [
            AfterSheet::class => function(AfterSheet $event) use(&$totalRows) {
                $sheet = $event->sheet;

                // Set border
                $lastRow = $sheet->getDelegate()->getHighestRow();
                $lastColumn = $sheet->getDelegate()->getHighestColumn();
                $range = 'A1:' . $lastColumn . $lastRow;
                $sheet->getDelegate()->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle('thin');

                // Align vertical center to all text
                $sheet->getDelegate()->getStyle('A1:' . $lastColumn . $lastRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                
                // Set font size
                $sheet->getDelegate()->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setSize(10);

                // Styling header 
                $headerCellRange = 'A1:F1';
                $sheet->getDelegate()->getStyle($headerCellRange)->getFont()->setBold(true);
                $sheet->getDelegate()->getStyle($headerCellRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Wrap text inside column B
                $wrapColumns = ['B'];
                foreach ($wrapColumns as $column) {
                    $range = $column . '1:' . $column . $lastRow;
                    $sheet->getDelegate()->getStyle($range)->getAlignment()->setWrapText(true);
                }
            }
        ];
    }
}
