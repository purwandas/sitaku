<?php

namespace App\Exports;

use \App\Purchase;
use App\Components\Filters\PurchaseFilter;
use Illuminate\Http\Request;
use \PDF;

class PurchaseExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new PurchaseFilter(new Request($params));
		$data   = Purchase::join('users', 'users.id', 'purchases.user_id')
			->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
			->select('users.name as user_name', 'suppliers.name as supplier_name', 'purchases.date', 'purchases.total_payment', 'purchases.total_paid', 'purchases.total_change')
			->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['USER NAME','text'],
				['SUPPLIER NAME','text'],
				['DATE','-'],
				['TOTAL PAYMENT','number'],
				['TOTAL PAID','number'],
				['TOTAL CHANGE','number']
			],
			'columns' => [
				'user_name', 'supplier_name', 'date', 'total_payment', 'total_paid', 'total_change'
			],
			'modelName' => "Purchase"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'landscape')
	        ->save(public_path($fileName));
	}
}