<?php

namespace App\Exports;

use \App\Role;
use App\Components\Filters\RoleFilter;
use Illuminate\Http\Request;
use \PDF;

class RoleExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new RoleFilter(new Request($params));
		$data   = Role::orderBy('roles.id','desc')->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['NAME','text']
			],
			'columns' => [
				'name'
			],
			'modelName' => "Role"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}