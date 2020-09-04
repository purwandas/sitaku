<?php

namespace App\Exports;

use \App\Category;
use App\Components\Filters\CategoryFilter;
use Illuminate\Http\Request;
use \PDF;

class CategoryExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new CategoryFilter(new Request($params));
		$data   = Category::filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['NAME','text']
			],
			'columns' => [
				'name'
			],
			'modelName' => "Category"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}