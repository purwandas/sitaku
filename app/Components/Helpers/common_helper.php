<?php
use App\Http\Helpers\S3Upload;
use App\Jobs\UniversalJob;
use App\JobTrace;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

if(!function_exists('rand_color')){
    function rand_color()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}

if(!function_exists('currency')){
    function currency($number, $type = 'IDR')
    {
        return $type.' '.number_format($number);
    }
}

if(!function_exists('get_auth')){
    function get_auth()
    {
    	$auth = collect('');
        $auth->id = 1;
        return $auth;
        return \Auth::user();
    }
}

if(!function_exists('getAutoNumeric')){
    function getAutoNumeric($value)
    {
    	return str_replace(',', '.', str_replace('.', '', $value) );
    }
}

if(!function_exists('moveArrayElement')){
	function moveArrayElement(&$array, $a, $b) {
	    $out = array_splice($array, $a, 1);
	    array_splice($array, $b, 0, $out);
	}
}

if(!function_exists('get_currency')){
    function get_currency($value, $front = 'Rp', $back = '')
    {
        return $front .' '. numbering_ifexist($value) . $back;
    }
}

if(!function_exists('currency_ifexist')){
    function currency_ifexist($value, $front = 'Rp', $back = '', $ifEmpty = '-')
    {
        return !empty($value) && is_numeric($value) ? get_currency($value, $front, $back) : $ifEmpty;
    }
}

if(!function_exists('numbering')){
    function numbering($value, $decimalPlace = 0){
        return number_format($value, $decimalPlace, ',', '.');
    }
}

if(!function_exists('numbering_ifexist')){
    function numbering_ifexist($value, $decimalPlace = '', $ifEmpty = '-'){
        return !empty(@$value) && is_numeric($value) ? numbering_decimal($value, $decimalPlace) : $ifEmpty;
    }
}

if(!function_exists('numbering_decimal')){
    function numbering_decimal($value, $decimalPlace = ''){
		$decimalPlace = !empty($decimalPlace) ? $decimalPlace : strlen(substr(strrchr($value, "."), 1));
        return numbering($value, $decimalPlace);
    }
}

if(!function_exists('get_input_type')){
    function get_input_type($value)
    {
		$textType     = ['string'];
		$numberType   = ['integer','numeric'];
		$emailType    = ['email'];
		$passwordType = ['password'];
		$fileType     = ['file'];
		$dateType     = ['date', 'datetime'];

        if(count(array_intersect($value, $textType)) > 0) return 'text';
        if(count(array_intersect($value, $numberType)) > 0) return 'number';
        if(count(array_intersect($value, $emailType)) > 0) return 'email';
        if(count(array_intersect($value, $passwordType)) > 0) return 'password';
        if(count(array_intersect($value, $fileType)) > 0) return 'file';
        if(count(array_intersect($value, $dateType)) > 0) return 'date';

        return 'text';
    }
}

if(!function_exists('setFileName')){
	function setFileName($text, $length = 140)
    {
        $stringExplode = explode('(',$text);
        $string = $stringExplode[0];

        return substr($string,0,$length).(strlen($string)>$length?'...':'').'('.$stringExplode[1];
    }
}

if(!function_exists('checkFileExist')){
	function checkFileExist($id)
    {
        $upload     = JobTrace::whereId($id)->first();
        $starMark   = "<label style=';color: red;'>*</label>";

        if (!$upload) {
            return $starMark;
        }

        $path_ = $upload->file_path."/".$upload->file_name;
        return file_exists($path_) ? "<a target='_blank' href='".asset($path_)."' class='btn btn-outline-primary' ><i class='si si-cloud-download mr-2'></i></a>" : $starMark;
    }
}

if(!function_exists('convertTraceStatus')){
	function convertTraceStatus($status, $rowId)
    {
        $btn = '';
        if ($status == 'PROCESSING') {
            $btn = 'primary';
        }else if ($status == 'DONE') {
            $btn = 'success';
        }else if ($status == 'FAILED') {
            $btn = 'danger';
        }
        return $label = "<label style='cursor: default;' class='btn btn-sm btn-".$btn."'>".$status."</label>";
        $label2 = "<label class='btn btn-sm btn-".$btn."'>".$status."</label>";
    }
}

if(!function_exists('toArrayEdit')){
	function toArrayEdit($key, $onEditKey='')
    {
    	if (!is_array($onEditKey)) {
			$len       = strlen($key);
			if (substr($key, -3) == "_id") {
				$field     = substr($key, 0, ($len-3));
				$onEditKey = [$field."_id", $field."_name"];
			} else {
				$onEditKey = [$key, $key."_text??data.".$key];
			}
		}
		$onEditKeyArr  = array_map(function($val) { return "data.$val"; }, $onEditKey);

		return $onEditKeyArr  = implode(',', $onEditKeyArr);
    }
}

if(!function_exists('toAssignedString')){
	function toAssignedString($columns, $exceptForeign = [], $import = null)
    {
    	$result = [];
    	foreach ($columns as $key => $column) {
			$foreign = Str::endsWith($column,'_id');

    		$val = !empty($import) && $foreign && !in_array($column, $exceptForeign) ? 
    			'$'.(substr($column, 0, strlen($column) -3)).'->id' : '$row[\''.$column.'\']';

    		$result[] = '\''.$column.'\' => '.$val;
    	}

    	return implode(",\n\t\t\t", $result);
    }
}

if(!function_exists('toColumnHeader')){
	function toColumnHeader($columns, $exceptForeign = [], $export = null, $template = null, $model = null)
    {
    	$result = [];
    	foreach ($columns as $key => $column) {
			$foreign = Str::endsWith($column,'_id');
			$field   =  $foreign && !empty($export) ? substr($column, 0, strlen($column) -3) : $column;
			if ($foreign && $template && !in_array($column, $exceptForeign)) {
				$field    = substr($column, 0, strlen($column) -3);
				$function = "_".$field;
				$newModel = $model::$function();
				$field    = $field.' '.$newModel::labelText()[0];
			}
			$result[] = '\''.strtoupper( str_replace("_", " ", $field) ).'\'';
    	}

    	return implode(", ", $result);
    }
}

if(!function_exists('toRelationFinder')){
	function toRelationFinder($columns, $model, $exceptForeign = [])
	{
		$result = [];

		foreach ($columns as $key => $column) {
			$foreign = Str::endsWith($column,'_id');
			if ($foreign && !in_array($column, $exceptForeign)) {
				$field    = substr($column, 0, strlen($column) -3);
				$function = "_".$field;
				$newModel = $model::$function();
				$result[] = '$'.$field.' = '.$newModel.'::where(\''.$newModel::labelText()[0].'\',$row[\''.$field.'_'.$newModel::labelText()[0].'\'])->first();';
			}
		}

		return implode("\n\t\t", $result);
	}
}

if(!function_exists('isForeign')){
	function isForeign($column, $exceptForeign = [])
	{
		$status = false;
		$foreign = Str::endsWith($column,'_id');
		if ($foreign && !in_array($column, $exceptForeign)) {
			$column = substr($column, 0, strlen($column) -3);
			$status = true;
		}

		return ['status' => $status, 'column' => $column];
	}
}

if(!function_exists('getForeigns')){
	function getForeigns($columns, $model, $exceptForeign = [])
	{
		$result = [];
		$index = 0;
    	foreach ($columns as $key => $column) {
			$foreign = Str::endsWith($column,'_id');
			if ($foreign && !in_array($column, $exceptForeign)) {
				$field    = substr($column, 0, strlen($column) -3);
				$function = "_".$field;
				$newModel = $model::$function();
				$label    = $newModel::labelText()[0] ?? "LABEL";
				$select   = stringToTable($field).".". ($label)." as ".($field."_".$label);
				$field    = str_replace("_", " ", ($field." ".$label) );
				
				$result[$index]['model']  = $newModel;
				$result[$index]['select'] = $select;
				$result[$index]['column'] = '\''.strtoupper( $field ).'\'';

				$index++;
			}
    	}

    	return $result;
	}
}

if(!function_exists('getForeignClass')){
	function getForeignClass($model, $foreignFunction)
	{
		$foreignFunction = '_'.$foreignFunction;
		return $model::$foreignFunction();
	}
}


if(!function_exists('generateDefaultEloquent')){
	function generateDefaultEloquent($model, $name, $columns, $rules, $exceptForeign = [], $export = null)
	{
		$join      = [];
		$fields    = [];
		$mainTable = stringToTable( Str::snake($name) );

		if (empty($export)) {
			$fields[]  = "'".$mainTable.".*'";
		}

		foreach ($columns as $key => $column) {
			$foreign   = Str::endsWith($column,'_id');
			$connector = $key == 0 ? "" : "\t";

	    	if ($foreign && !in_array($column, $exceptForeign)) {
	    		$field    = substr($column, 0, strlen($column) -3);
				$function = "_".$field;
				$newModel = $model::$function();
				$label    = $newModel::labelText()[0] ?? "LABEL";
				$table    = stringToTable( $field );
				$fields[] = "'".$table.".".$label." as ".$field."_".$label."'";

				$rule = empty($rule[$key]) ? "" : explode("|", $rule[$key]);
				$rule = is_array($rule) ? (!in_array('required', $rules) || in_array('nullable', $rules)) : false; //true mean nullable field
				$rule = $rule ? "leftJoin" : "join";
				$join[] = (count($join) == 0 ? "" : "->").$rule."('$table', '$table.id', '$mainTable.$column')";
	    	} elseif (!empty($export)) {
				$fields[]  = "'".$mainTable.".".$column."'";
			}
	    }

	    $select = implode(", ", $fields);
	    $select = count($fields) > 1 ? "\t\t\t".( count($join) > 0 ? "->" : "" )."select(".$select.")\n\t\t\t->" : '';

	    // $order = "orderBy('".$mainTable.".id','desc')->";
	    $order = "";

	    $join = implode("\n\t\t\t", $join).(count($join) > 0 ? "\n" : '');

	    return ["join" => $join, 'select' => $select.$order];
	}
}

if (!function_exists('processing_jobs')) {
    /**
     * Run Tenant Migrations in the connected principal database.
     */
    function processing_jobs($param, $filters = null, $model = null, $module = null, $path = null)
    {
        if (is_array($param)) {
            $exp = explode('\\', $param['model']);

			$title   = $param['title'] ?? preg_replace('/(?<! )(?<!^)(?<![A-Z])[A-Z]/', ' $0', end($exp));
			$filters = @$param['filters'];
			$model   = $param['model'];
			$module  = $param['module'];
			$path    = $param['path'];
			$ext     = substr(@$param['path'],-4) == 'xlsx' ? 'xlsx' : 'pdf';
        } else {
			$exp   = explode('\\', $model);
			$title = $param ?? preg_replace('/(?<! )(?<!^)(?<![A-Z])[A-Z]/', ' $0', end($exp));
			$ext   = explode('\\', @$path);
			$ext     = substr(@$path,-4) == 'xlsx' ? 'xlsx' : 'pdf';
        }

        $type = explode('/', $path)[0];

        if ($type == 'imports') {
            $request = request();
            $request->validate([
                'file' => 'required|file'
            ]);
            $file = $request->file('file');
        }

        $name = '';

        if ($type == 'imports') {
            $name = $title . " (@" . Carbon::now()->format('Ymdhis') . ") " . $file->getClientOriginalName();
        } else {
            $name = $title . " (@" . substr(str_replace("-", null, crc32(md5(time()))), 0, 9) . ').'. @$ext ?? 'xlsx';
        }

        $filePath = $path . '/' . $name;

        if ($type == 'imports') {
            Storage::disk('public')->putFileAs($path, $file, $name);
        }

        $trace = JobTrace::create([

			'user_id'   => get_auth()->id,

			// 'user_id'   => \Auth::user(),

			'title'     => $title,
			'model'     => $model,
			'module'    => $module,
			'file_path' => $filePath,
			'status'    => 'PROCESSING',
        ]);

        dispatch(new UniversalJob($trace, $filters, $type, $ext ?? 'xlsx'));

        return $trace;
    }
}

if(!function_exists('modelToTitle')){
	function modelToTitle($model)
	{
		return ucwords( str_replace("_", " ", Str::snake($model) ) );
	}
}

if(!function_exists('generateTextFilter')){
	function generateTextFilter($columns, $rule, $model, $exceptForeign = [])
	{
		$function = [];
		$snake    = Str::snake($model);
		$table    = stringToTable( $snake );

		$additionalNamespace = "";
		$connector = "\t";

		foreach ($columns as $key => $column) {
			$rules     = !empty($rule[$key]) ? explode('|', $rule[$key]) : [];
			$foreign   = Str::endsWith($column,'_id');
			// $connector = $key == 0 ? "" : "\t";

	    	if (in_array('date', $rules) || in_array('datetime', $rules)) {
				$function[] = $connector.
					'public function '.$column.'($value)'."\n".
					"\t".'{'."\n".
					"\t\t".'$value = explode(\' ~ \', $value);'."\n".
					"\t\t".'$begin = Carbon::parse($value[0])->format(\'Y-m-d\');'."\n".
					"\t\t".'$end   = Carbon::parse($value[1])->format(\'Y-m-d\');'."\n".
					"\t\t".'return $this->builder->whereBetween(\''.$table.'.'.$column.'\', [$begin, $end]);'."\n".
					"\t".'}'."\n";
				$additionalNamespace .= "use Carbon\Carbon;\n";
			} elseif ($foreign && !in_array($column, $exceptForeign)) {
				$fnName     = substr($column, 0, strlen($column) -3);
				$tableF     = stringToTable( $fnName );
				$function[] = $connector.
					'public function '.$column.'($value)'."\n".
					"\t".'{'."\n".
					"\t\t".'return is_array($value) ? $this->builder->whereIn(\''.$tableF.'.id\', $value) : $this->builder->where(\''.$tableF.'.id\', $value);'."\n".
					"\t".'}'."\n";
	    	} else {
	    		$function[] = $connector.
					'public function '.$column.'($value)'."\n".
					"\t".'{'."\n".
					"\t\t".'return is_array($value) ? $this->builder->whereIn(\''.$table.'.'.$column.'\', $value) : $this->builder->where(\''.$table.'.'.$column.'\', $value);'."\n".
					"\t".'}'."\n".
					"\t".'public function _'.$column.'($value)'."\n".
					"\t".'{'."\n".
					"\t\t".'return $this->builder->where(\''.$table.'.'.$column.'\', \'like\', \'%\'.$value.\'%\');'."\n".
					"\t".'}'."\n";
	    	}

		}

		array_unshift($function,
			'public function '.$snake.'($value)'."\n".
			"\t".'{'."\n".
			"\t\t".'return is_array($value) ? $this->builder->whereIn(\''.$table.'.'.$columns[0].'\', $value) : $this->builder->where(\''.$table.'.'.$columns[0].'\', $value);'."\n".
			"\t".'}'."\n\n".
			"\t".'public function groupBy($value)'."\n".
			"\t".'{'."\n".
			"\t\t".'return $this->builder->groupBy($value);'."\n".
			"\t".'}'."\n");

		$function = implode("\n", $function);

		$additionalNamespace = !empty($additionalNamespace) ? "\n".$additionalNamespace : "";

    	return [$function, $additionalNamespace];
	}
}

if(!function_exists('removeArrayByKey')){
	function removeArrayByKey(&$array, $keys)
	{
	    foreach($keys as $value){
	        unset($array[$value]);
	    }
    }
}

if(!function_exists('generateTextRule')){
	function generateTextRule($columns, $rules, $modelNameSpace, $exceptForeign = [])
	{
		$result   = [];
		$function = [];

		foreach ($columns as $key => $column) {
			$foreign   = Str::endsWith($column,'_id');
			$connector = $key == 0 ? "" : "\t\t\t";

	    	if ($foreign && !in_array($column, $exceptForeign)) {
				$fnName            = substr($column, 0, strlen($column) -3);
				$table             = stringToTable( $fnName );
				$camel             = ucfirst(Str::camel( $fnName ));
				$exp               = explode('\\', $modelNameSpace);
				$newModelNamespace = substr($modelNameSpace,0, -1 * strlen(end($exp))) . $camel;

				$result[]   = $connector."'".$column."' => 'exists:".$table.",id',\n";
				$function[] = (count($function) == 0 ? "" : "\t").
					'public function '.$fnName.'()'."\n".
					"\t".'{'."\n".
					"\t\t".'return $this->belongsTo($this->_'.$fnName.'(), \''.$column.'\');'."\n".
					"\t".'}'."\n\n".
					"\t".'public static function _'.$fnName.'()'."\n".
					"\t".'{'."\n".
					"\t\t".'return \'\\'.$newModelNamespace.'\';'."\n".
					"\t".'}'."\n";
	    	} else {
				$nullable = (in_array('required', $rules) || !in_array('nullable', $rules)) ? 'required' : 'nullable';
				$result[] = $connector."'".$column."' => '".( empty($rules[$key]) ? "string|".$nullable : $rules[$key] )."',\n";
	    	}
		}

		//generate static function for select2 text on the child's filter
		$function[] = (count($function) == 0 ? "" : "\t"). 
			'public static function labelText()'."\n".
			"\t".'{'."\n".
			"\t\t".'return [\''.$columns[0].'\'];'."\n".
			"\t".'}'."\n";

		$result   = implode('', $result);
		$function = implode("\n", $function);

    	return ['rules' => $result, 'function' => $function];
	}
}

if(!function_exists('ruleToMigrationColumn')){
	function ruleToMigrationColumn($column, $dType, $rule, $exceptForeign = [])
    {
		$columns   = [];
		$type      = 0;
		$connector = "";
		$rules     = !empty($rule) ? explode('|', $rule) : [];
		$nullable  = (in_array('required', $rules) || !in_array('nullable', $rules)) ? '->required()' : '->nullable()';

    	$foreign = Str::endsWith($column,'_id');
    	if ($foreign && !in_array($column, $exceptForeign)) {
			$table     = stringToTable( substr($column, 0, strlen($column) -3) );
			$columns[] = "\n"."\t\t\t\t".'$table->unsignedBigInteger(\''.$column.'\')'.
				$nullable.
				";\n";
			$columns[] = "\t\t\t\t".'$table->foreign(\''.$column.'\')->references(\'id\')->on(\''.$table.'\')';
    	} else {
	    	$tmp = "";
	    	$tmp .= '$table';

	    	if (!empty($dType)) {
	    		if (Str::contains($dType, '(')) {
	    			$dTtmp = explode('(', $dType);
	    			$tmp   .= "->".$dTtmp[0]."('".$column."',".(Str::replaceLast(')', '', $dTtmp[1])).")";
	    		} else
				$tmp   .= "->".$dType."('".$column."')";
	    		$type ++;
	    	} elseif (in_array("date", $rules)) {
	    		$tmp .= "->date('".$column."')";
	    		$type ++;
	    	} elseif (in_array("datetime", $rules)) {
	    		$tmp .= "->datetime('".$column."')";
	    		$type ++;
	    	} elseif (in_array("numeric", $rules)) {
	    		$tmp .= "->integer('".$column."')";
	    		$type ++;
	    	}

	    	if ($type == 0) {
				$tmp .= "->string('".$column."')";
	    	}
	    	$columns[] = $tmp.$nullable;
	    }

		return implode($connector, $columns).';'."\n";
    }
}

if(!function_exists('toColumnPdf')){
	function toColumnPdf($columns, $rules, $model, $exceptForeign = [])
    {
		$result     = [];
		$columnName = [];
		$type       = "-";

    	foreach ($columns as $key => $column) {
			$rule    = !empty($rules[$key]) ? explode('|', $rules[$key]) : [];
			$foreign = Str::endsWith($column,'_id');
			$field   =  $foreign && !empty($export) ? substr($column, 0, strlen($column) -3) : $column;

			if ($foreign && !in_array($column, $exceptForeign)) {
				$field    = substr($column, 0, strlen($column) -3);
				$function = "_".$field;
				$newModel = $model::$function();
				$field    = $field.'_'.$newModel::labelText()[0];
				$type     = "text";
			} else {
				if (in_array('date', $rule)) {
		    		$type = "-";
		    	} elseif (in_array('datetime', $rule)) {
		    		$type = "-";
		    	}elseif (in_array('numeric', $rule)) {
		    		$type = "number";
		    	} else {
		    		$type = "text";
		    	}
			}

			$result[]     = '[\''.strtoupper( str_replace("_", " ", $field) ).'\',\''.$type.'\']';
			$columnName[] = '\''.strtolower( $field ).'\'';
    	}

    	return [
			'header'  => implode(",\n\t\t\t\t", $result), 
			'columns' => implode(", ", $columnName)
    	];
    }
}

if(!function_exists('stringToTable')){
	function stringToTable($string)
    {
    	return Str::plural($string);
        return Str::endsWith($string,'y') ? 
        	substr($string, 0, strlen($string) - 1).'ies' : 
        	( Str::endsWith($string,'s') ? $string : $string.'s' );
    }
}

if(!function_exists('numberToAlphabet')){
    function numberToAlphabet($number) //support up to ZZ
    {
        $alphabet = range('A', 'Z');
        $static = 26;

        $front = floor($number / $static) - 1;
        $number %= $static;

        return $front >= 0 ? $alphabet[$front].$alphabet[$number] : $alphabet[$number];
    }
}

if(!function_exists('createFileCode')){
    function createFileCode()
    {
        return "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
    }
}

if(!function_exists('dirExists')){
    function dirExists($fileName)
    {
		$filePath = explode("/",$fileName);
        array_pop($filePath);
        $tmpPath = public_path('');
        foreach ($filePath as $key => $value) {
        	$tmpPath .= '/'.$value;
	        if(!is_dir($tmpPath)) mkdir($tmpPath);
        }
    }
}

if(!function_exists('generateCustomHelper')){
    function generateCustomHelper($exceptForeign = [])
	{
		if (count($exceptForeign) > 0) {
			array_shift($exceptForeign);

			$exceptForeign = array_map(function($val) { return "'$val'"; }, $exceptForeign);
			$exceptForeign = implode(",", $exceptForeign);
			return "->setExceptForeign([$exceptForeign])";
		}
		
        return "";
	}
}

if(!function_exists('processPath')){
    function processPath()
	{
	  	$path = [];
        foreach (\Route::getRoutes() as $key => $value) {
            $route = $value->getName();
            $prefix = $value->getPrefix();
            if (Str::endsWith($route,"index") && !Str::startsWith($route,"passport")) {
                if (!empty($prefix)) {
                    $keys      = explode('/', $prefix);
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
        return $path;
	}
}

if(!function_exists('labelize')){
    function labelize($text, $separator = '')
	{
		$label = '';
		if($separator == ''){
			$label = ucwords($text);
		}else{
			$label = ucwords(str_replace('-', ' ', $text));
		}
        return $label;
	}
}

if(!function_exists('getFilterText')){
    function getFilterText($anotherTerm = [])
    {
    	$request = request();
        $filterText = [];

        // if(isset($request->byMonth)){
        //     $filterText[] = $request->byMonth;
        // }
        // if(isset($request->searchMonth)){
        //     $filterText[] = $request->searchMonth;
        // }
        // if(isset($request->byFromDate)){
        //     $filterText[] = $request->byFromDate.' to '.$request->byToDate;
        // }
        // if(isset($request->byStore)){
        //     $dataFilter = Store::whereIn('id',$request->byStore)->pluck('store_id')->toArray();
        //     $filterText[] = implode(', ', $dataFilter);
        // }

        // if(isset($request->byBrand)){
        //     if (in_array('brand-comp', $anotherTerm)) {
        //         $dataFilter = Competitor::whereIn('id',$request->byBrand)->pluck('name')->toArray();
        //         $dataFilter = implode(', ', $dataFilter);
        //     }else if (in_array('brand-ds', $anotherTerm)) {
        //         $dataFilter = is_array($request->byBrand) ? implode(' ', $request->byBrand) : $request->byBrand;
        //     }else{
        //         $dataFilter = Cluster::whereIn('id',$request->byBrand)->pluck('name')->toArray();
        //         $dataFilter = implode(', ', $dataFilter);
        //     }
        //     $filterText[] = $dataFilter;
        // }
        // if(isset($request->byProduct)){
        //     if (in_array('product-alt', $anotherTerm)) {
        //         $dataFilter = is_array($request->byProduct) ? implode(' ', $request->byProduct) : $request->byProduct;
        //     }else{
        //         $dataFilter = Product::whereIn('id',$request->byProduct)->pluck('name')->toArray();
        //         $dataFilter = implode(', ', $dataFilter);
        //     }
        //     $filterText[] = $dataFilter;
        // }
        // if(isset($request->byImprovementType)){
        //     $filterText[] = is_array($request->byImprovementType) ? implode(' ', $request->byImprovementType) : $request->byImprovementType;
        // }

        return $filterText;
    }
}

if(!function_exists('summernoteConversion')){
    function summernoteConversion($request,$folder)
    {
    	$detail=$request;

    	Validator::make(['content' => $detail],[
    		'content' => 'required|string|profane:en,id'
    	])->validate();

    	libxml_use_internal_errors(true);

		$dom = new \domdocument();
		$dom->loadHtml($detail, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		$images = $dom->getelementsbytagname('img');

        //loop over img elements, decode their base64 src and save them to public folder,
        //and then replace base64 src with stored image URL.
		foreach($images as $k => $img){
			$data = $img->getattribute('src');

			if(!Str::startsWith($data,asset(Storage::url($folder)))){
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);

				$data = base64_decode($data);
				$image_name= time().$k.'.png';
				$path = $folder .'/'. $image_name;

				// Store to storage folder
				Storage::put($path, $data);
				// file_put_contents($path, $data);

				$img->removeattribute('src');
				$img->setattribute('src', asset(Storage::url($path)));
			}
		}

		$detail = $dom->savehtml();

    	return $detail;
    }
}

if(!function_exists('deleteOldSummernoteImage')){
    function deleteOldSummernoteImage($content,$request = null)
    {
    	libxml_use_internal_errors(true);

    	$dom = new \domdocument();
        $dom->loadHtml($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getelementsbytagname('img');

        $arrNewImage = [];
        if($request !== null){
	        $newDom = new \domdocument();
	        $newDom->loadHtml($request, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	        $newImages = $newDom->getelementsbytagname('img');
	        foreach($newImages as $k => $img){
	            $data = $img->getattribute('src');
	        	$arrNewImage[] = $data;
	        }
        }

        $arrImage = [];
        foreach($images as $k => $img){
            $data = $img->getattribute('src');
            if(!in_array($data,$arrNewImage)){
	            $arrImage[] = 'public'.'/'.ltrim($data,asset(Storage::url('')));
            }
        }
        Storage::delete($arrImage);

		return true;
    }
}

if(!function_exists('upload_file')){
    function upload_file($file,$folder)
    {
    	$uniqueId = strtoupper(substr(chunk_split(hash('md5', time()), 4, '-'), 0, -1));
    	if (strstr($file->getClientMimeType(), 'image/')) {
            $file_name = $uniqueId.'.'.$file->getClientOriginalExtension();
            $compress = Image::make($file)->orientate()->save(public_path($file_name), 50);

            return S3Upload::doUpload($file_name, $folder);
        }

        if (strstr($file->getClientMimeType(), 'video/') || strstr($file->getClientMimeType(), 'application/octet-stream')) {
            $file_name = $uniqueId.'.mp4';
            $file_size = $file->getClientSize();
            $file_mime_type = $file->getClientMimeType();

            if ($file_size > 25252207) {
                $file->move(storage_path('app'), '~'.$file_name);

                FFMpeg::open('~'.$file_name)
                ->export()
                ->inFormat((new \FFMpeg\Format\Video\X264('libmp3lame', 'libx264'))->setKiloBitrate(500))
                ->save($file_name);

                $location_cdn = S3Upload::doUpload($file_name, $folder, storage_path('app'));
            } else {
                $file->move(public_path(), $file_name);
                $location_cdn = S3Upload::doUpload($file_name, $folder);
            }

            return $location_cdn;
        }
    }
}

if(!function_exists('delete_file')){
    function delete_file($folder,$path)
    {
    	try{
	    	$folderNamespace = 'ruangobrol/rumi/'.$folder;
	    	if(is_array($path)){
	    		foreach ($path as $value) {
			    	$name = explode($folderNamespace, $value)[1];
	    			$pathName = $folderNamespace.$name;
		    		Storage::disk('spaces')->delete($pathName);
	    		}
	    	}else{
		    	$name = explode($folderNamespace, $path)[1];
		    	$pathName = $folderNamespace.$name;
		    	Storage::disk('spaces')->delete($pathName);
	    	}

    	}catch(\Exception $ex){
	    	throw new FileException($ex, 1);
    	}
    }
}

if(!function_exists('arrayToHtml')){
    function arrayToHtml($attributes){
        if (empty($attributes))
            return '';
        if (!is_array($attributes))
            return $attributes;

        $attributePairs = [];
        foreach ($attributes as $key => $val)
        {
            if (is_int($key))
                $attributePairs[] = $val;
            else
            {
                $val = htmlspecialchars($val, ENT_QUOTES);
                $attributePairs[] = "{$key}=\"{$val}\"";
            }
        }

        return join(' ', $attributePairs);
    }
}

if(!function_exists('getSvgIcon')){
	function getSvgIcon($icon, $addClass = '') {
		return getFaIcon($icon, $addClass);
        return 
        "<svg class='c-icon". (!empty($addClass) ? ' '.$addClass : '') ."'>
			<use xlink:href='".asset('assets/vendors/@coreui/icons/svg/free.svg#'.$icon). "'></use>
		</svg>";
    }
}
if(!function_exists('getFaIcon')){
	function getFaIcon($icon, $addClass = '') {
		return "<i class='fas $icon'></i>";
	}
}

if(!function_exists('displayMenu')){
	function displayMenu($array = [], $configLabel = [], $icon = '') {
        echo "
	        <li class='". (array_key_exists('url', $array) && \Route::currentRouteName() == $array['url'] ? "active" : ""). "'>
		        <a href='".route($array['url'])."'>".displayMenuLabel($array['label'], $configLabel, $icon)."</a>
	        </li>
        ";
    }
}

if(!function_exists('displayMenuLabel')){
	function displayMenuLabel($label, $configLabel = [], $icon = '') {
        $result = "";
        $result .= !empty($icon) ? "<i class='$icon'></i><span>" : "";
        $result .= getMenuLabel($configLabel, $label);
        $result .= !empty($icon) ? "</span>" : "";

        return $result;
    }
}

if(!function_exists('createMenuSeparator')){
	function createMenuSeparator($label) {
        $result = "";
        $result .= "<li class='menu-title'>";
        $result .= $label;
        $result .= "</li>";
        
        return $result;
    }
}

if(!function_exists('getMenuLabel')){
	function getMenuLabel($array = [], $label) {
        $key = Str::camel($label);

		if ( count($array) > 0 && array_key_exists($key, $array) ) {
            $label = $array[$key];
        }

        return $label;
    }
}

if(!function_exists('getConfigMenu')){
	function getConfigMenu($type) {
		if ($type == 'icon') {
	        $configIcon = [];
		    $configIcon['user'] = "fas fa-users-cog";
		    $configIcon['role'] = "fas fa-address-card";
		    $configIcon['category'] = "far fa-caret-square-right";
		    $configIcon['product'] = "fas fa-file-medical";
		    $configIcon['production'] = "fas fa-industry";
		    $configIcon['supplier'] = "fab fa-supple";

		    return $configIcon;
		}

		if ($type == 'separator') {
		    $configSeparator = [];
		    $configSeparator['role'] = "MASTER DATA";
		    $configSeparator['purchase'] = "REPORT";
		    $configSeparator['purchase-form'] = "TRANSACTION DETAIL";
		    return $configSeparator;
		}

		if ($type == 'label') {
		    $configLabel = [];
		    // $configLabel['role'] = "Permission";
		    return $configLabel;
		}

		if ($type == 'text') {
		    $configLabel = [];
		    $configLabel['role'] = "Permission";
		    $configLabel['sales-form'] = "Sales";
		    $configLabel['purchase-form'] = "Purchase";
		    return $configLabel;
		}
    }
}