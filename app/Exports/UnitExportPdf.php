<?php

namespace App\Exports;

use \App\Unit;
use App\Components\Filters\UnitFilter;
use Illuminate\Http\Request;
use \PDF;

class UnitExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new UnitFilter(new Request($params));
		$data   = Unit::			select('units.name', 'units.conversion')
			->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['NAME','text'],
				['CONVERSION','number']
			],
			'columns' => [
				'name', 'conversion'
			],
			'modelName' => "Unit"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}