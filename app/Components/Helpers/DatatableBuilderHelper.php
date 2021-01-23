<?php

namespace App\Components\Helpers;

/**
 * 
 */
class DatatableBuilderHelper
{
	public static function button($button, $url = '#')
	{
		$buttonTemplates = config('datatable-builder')['buttonTemplates'];

		if (is_array($button)) {
			$stringButton = '';
			foreach ($button as $buttonName => $param) {
				$explodeButtonName = explode('-', $buttonName);
				if (count($explodeButtonName) > 1) {
					$curBtn = $buttonTemplates[$explodeButtonName[0]];
				} else {
					$curBtn = $buttonTemplates[$buttonName];
				}

				if (is_array($param)) {
					foreach ($param as $key => $value) {
						$curBtn = str_replace("<<$key>>", $value, $curBtn);
						$curBtn = str_replace("#$key#", $value, $curBtn);
					}
				} else {
					$curBtn = str_replace('<<url>>', $param, $curBtn);
				}

				$stringButton .= $curBtn;
			}

			return $stringButton;
		}

		return str_replace('<<url>>', $url, $buttonTemplates[$button]);
	}

	public static function render($params)
	{
		return view('components.minify_datatable',$params);
	}
}