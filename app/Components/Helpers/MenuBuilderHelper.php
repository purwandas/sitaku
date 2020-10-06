<?php

namespace App\Components\Helpers;

use Illuminate\Support\Str;

class MenuBuilderHelper
{

	public static function getMenu($config = [],$permission = [])
	{
		$path = [];
		if (count($path) == 0 ) {
			$path = [];
	        foreach (\Route::getRoutes() as $key => $value) {
	            $route = $value->getName();
	            $prefix = $value->getPrefix();
	            if (Str::endsWith($route,"index") && !Str::startsWith($route,"passport")) {
	                if (!empty($prefix)) {
	                    $keys      = explode('/', ltrim($prefix,"/"));
	                    $value     = $route;
	                    $reference = &$path;
	                    foreach ($keys as $keyz) {
	                        if (!array_key_exists($keyz, $reference)) {
	                            $reference[$keyz] = [];
	                        }
	                        $reference = &$reference[$keyz];
	                    }
	                    $reference[] = $value;
	                    unset($reference);
	                } else {
	                    $path[] = $route;
	                }
	            }
	        }
	    }
		$menu = [];
        foreach ($path as $key => $value) {
            if (!is_array($value) || (is_array($value) && count($value) == 1)) { //child or single array
	        	$menu[] = self::getMenuArray($value, 3);
	        } else { //parent
	        	$sub = [];
	        	foreach ($value as $keyz => $valuez) {
	        		if (is_array($valuez)) {
		        		$sub = self::getSubMenu($keyz, $valuez);
	        		} else {
	        			$expV = explode('.',$valuez);
	        			$sub[] = self::getMenuArray($valuez, 1);
	        		}
	        	}
	        	
	        	$menu[] = self::getMenuArray($key, 2, $sub);
	        }
        }
        return $menu;
	}

	public static function getSubMenu($key, $value) {
		if (is_array($value)) { //parent
        	$sub = [];
        	foreach ($value as $keyz => $valuez) {
        		if (is_array($valuez)) {
	        		$sub = getSubMenu($keyz, $valuez);
        		} else {
        			$sub[] = getMenuArray($valuez, 1);
        			
        		}
        	}
        	return getMenuArray($key, 2, $sub);
        } else { //child
        	return getMenuArray($value, 3);
        }
	}

	public static function getMenuArray($value='', $type='', $sub=[])
	{
		if ($type == 1) {
			$key = substr($value, 0, strlen($value)-6);
			$label  = labelize($key,'-');
			$expV   = explode('.',$value);
			$result = [
				"key"  => $key,
				"text" => $label,
				"url"  => route($value),
				"key"  => $expV[0]
			];
		} else if ($type == 2) {
			$label  = labelize($value,'-');
			$result = [
				"key"      => $value,
				"text"     => $label,
				"sub-menu" => $sub
        	];
		} else if ($type == 3) {
			$value = is_array($value) ? $value[0] : $value;
			$key = substr($value, 0, strlen($value)-6);
			$label  = labelize($key,'-');
			$result = [
				"key"  => $key,
				"text" => $label,
				"url"  => route($value)
        	];
		}

		return $result;
	}

}