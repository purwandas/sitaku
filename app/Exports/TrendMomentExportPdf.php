<?php

namespace App\Exports;

use \App\TrendMoment;
use App\Components\Filters\TrendMomentFilter;
use Illuminate\Http\Request;
use \PDF;

class TrendMomentExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new TrendMomentFilter(new Request($params));
		$data   = TrendMoment::			select('trend_moments.month_', 'trend_moments.year_', 'trend_moments.total_sales')
			->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['MONTH ','number'],
				['YEAR ','number'],
				['TOTAL SALES','number']
			],
			'columns' => [
				'month_', 'year_', 'total_sales'
			],
			'modelName' => "TrendMoment"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}