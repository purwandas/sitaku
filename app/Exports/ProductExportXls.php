<?php

namespace App\Exports;

use \App\Product;
use App\Components\Filters\ProductFilter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;

class ProductExportXls implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
        $filter = new ProductFilter(new Request(self::$params));
        return Product::join('categories', 'categories.id', 'products.category_id')
			->join('productions', 'productions.id', 'products.production_id')
			->select('products.name', 'products.stock', 'products.buying_price', 'products.selling_price', 'categories.name as category_name', 'productions.name as production_name')
			->filter($filter)->get();
    }

    public function headings(): array
    {
        return ['NAME', 'STOCK', 'BUYING PRICE', 'SELLING PRICE', 'CATEGORY ID', 'PRODUCTION ID'];
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
