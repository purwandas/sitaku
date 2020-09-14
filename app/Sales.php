<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    public static function rule(){
        return [
        	// Define rule here to display data on datatable and generate form builder
            // Example : 'name' => 'required|string|min:8|max:10',
            'date' => 'date',
			'supplier_id' => 'exists:suppliers,id',

        ];
    
    }

    public function supplier()
	{
		return $this->belongsTo($this->_supplier(), 'supplier_id');
	}

	public static function _supplier()
	{
		return '\\App\Supplier';
	}

	public static function labelText()
	{
		return ['date'];
	}

}
