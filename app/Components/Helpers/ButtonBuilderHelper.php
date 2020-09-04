<?php

namespace App\Components\Helpers;

/**
 * 
 */
class ButtonBuilderHelper
{
	public static function render($params)
	{
		$el = '';
		foreach ($params as $key => $url) {
			if($key == 'edit'){
				$el .= "<button onclick=\"editModalWriteSpace('".$url."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormWriteSpace' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>";
			}
            
            if($key == 'delete'){
            	$el .= "<button data-url=".$url." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            }

            if($key == 'detail'){
            	$el .= "<button onclick=\"viewDetailContent('".$url."','content')\" class='btn btn-sm btn-primary btn-square'><i class='fas fa-paperclip'></i></button>";
            }

            $el .= ' ';
        }

        return $el;
	}
}