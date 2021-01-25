<?php

namespace App\Exports;

use App\Components\Filters\SalesFilter;
use App\SalesDetail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use \App\Sales;
use DB;

class SalesExportXls implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
        $filter = new SalesFilter(new Request(self::$params));
        // $data   = Sales::join('users','users.id','sales.user_id')->select('sales.*','users.name as user_name')->filter($filter)->get();

        // foreach ($data as $key => $value) {
        //     $detail = SalesDetail::whereSalesId($value->id)->join('products','products.id','sales_details.product_id')->select('sales_details.*' ,'products.name as product')->get()->pluck('product')->toArray();

        //     $data[$key]['product'] = count($detail) ? implode(', ', $detail) : '-';
        // }

        $data = SalesDetail::join('sales','sales_details.sales_id','sales.id')
                        ->join('users','users.id','sales.user_id')
                        ->join('products','products.id','sales_details.product_id')
                        ->select('users.name as user_name','sales.date',DB::raw('group_concat(products.name) as product'),'sales.total_payment','sales.total_paid','sales.total_change')
                        ->filter($filter)
                        ->groupBy('sales_id')
                        ->get();

        return $data;
    }

    public function headings(): array
    {
        return ['USER', 'DATE', 'PRODUCT', 'TOTAL PAYMENT', 'TOTAL PAID', 'TOTAL CHANGE'];
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
