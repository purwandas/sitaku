<?php

namespace App\Exports;

use \App\ProductUnit;
use App\Components\Filters\ProductUnitFilter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;

class ProductUnitExportXls implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected static $params;

    public function __construct($params)
    {
        self::$params = $params;
    }

    public function collection()
    {
        $filter = new ProductUnitFilter(new Request(self::$params));
        return ProductUnit::join('products', 'products.id', 'product_units.product_id')
			->leftJoin('units', 'units.id', 'product_units.unit_id')
			->select('product_units.conversion', 'product_units.price', 'products.name as product_name', 'units.name as unit_name')
			->filter($filter)->get();
    }

    public function headings(): array
    {
        return ['CONVERSION', 'PRICE', 'PRODUCT ID', 'UNIT ID'];
    }

    public function registerEvents(): array
    {
    	$alphabet = range('A','Z');
    	$max = $alphabet[count($this->headings())-1];
        return [
            AfterSheet::class => function(AfterSheet $event) use($max){
                $cellRange = 'A1:'.$max.'1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                $event->sheet->setAutoFilter($cellRange);
                $event->sheet->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('809fff');
            },
        ];
    }
}
