<?php

namespace App\Exports;

use \App\Purchase;
use App\Components\Filters\PurchaseFilter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;

class PurchaseExportXls implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
        $filter = new PurchaseFilter(new Request(self::$params));
        return Purchase::join('users', 'users.id', 'purchases.user_id')
			->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
			->select('users.name as user_name', 'suppliers.name as supplier_name', 'purchases.date', 'purchases.total_payment', 'purchases.total_paid', 'purchases.total_change')
			->filter($filter)->get();
    }

    public function headings(): array
    {
        return ['USER ID', 'SUPPLIER ID', 'DATE', 'TOTAL PAYMENT', 'TOTAL PAID', 'TOTAL CHANGE'];
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
