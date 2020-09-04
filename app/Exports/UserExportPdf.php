<?php

namespace App\Exports;

use \App\User;
use App\Components\Filters\UserFilter;
use Illuminate\Http\Request;
use \PDF;

class UserExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new UserFilter(new Request($params));
		$data   = User::join('roles', 'roles.id', 'users.role_id')
			->select('users.name', 'users.email', 'users.password', 'roles.name as role_name')
			->orderBy('users.id','desc')->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['NAME','text'],
				['EMAIL','text'],
				['PASSWORD','text'],
				['ROLE NAME','text']
			],
			'columns' => [
				'name', 'email', 'password', 'role_name'
			],
			'modelName' => "User"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}