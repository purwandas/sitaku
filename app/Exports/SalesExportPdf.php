<?php

namespace App\Exports;

use App\Components\Filters\SalesFilter;
use App\SalesDetail;
use Illuminate\Http\Request;
use \App\Sales;
use \PDF;

class SalesExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new SalesFilter(new Request($params));
        $data   = Sales::join('users','users.id','sales.user_id')->select('sales.*','users.name as user_name')->filter($filter)->get();

        foreach ($data as $key => $value) {
            $detail = SalesDetail::whereSalesId($value->id)->join('products','products.id','sales_details.product_id')->select('sales_details.*' ,'products.name as product')->get()->pluck('product')->toArray();

            $data[$key]['product'] = count($detail) ? implode(', ', $detail) : '-';
        }

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['USER','text'],
				['DATE','-'],
				['PRODUCT','text'],
				['TOTAL PAYMENT','text'],
				['TOTAL PAID','text'],
				['TOTAL CHANGE','text'],
			],
			'columns' => [
				'user_name','date', 'product','total_payment','total_paid','total_change'
			],
			'modelName' => "Sales"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}