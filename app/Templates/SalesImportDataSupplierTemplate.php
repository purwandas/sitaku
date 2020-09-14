<?php

namespace App\Templates;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesImportDataSupplierTemplate implements FromQuery, WithTitle, WithHeadings, WithEvents, ShouldAutoSize
{

    /**
     * @return Builder
     */
    public function headings(): array
    {
        return ['SUPPLIER NAME'];
    }
    
    public function registerEvents(): array
    {
    	$alphabet = range('A','Z');
        $max = $alphabet[count($this->headings())-1];
        return [
            AfterSheet::class => function(AfterSheet $event) use($max,$alphabet){
                $cellRange = 'A1:'.$max.'1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                $event->sheet->getDelegate()->setAutoFilter($cellRange);
                $event->sheet->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('809fff');
            },
        ];
    }

    public function query()
    {
        return \App\Supplier::query()->select('suppliers.name as supplier_name');
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Supplier Data';
    }
}