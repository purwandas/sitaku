<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    public static function rule(){
        return [
        	// Define rule here to display data on datatable and generate form builder
            // Example : 'name' => 'required|string|min:8|max:10',
            'conversion' => 'string|required',
			'price' => 'string|required',
			'product_id' => 'exists:products,id',
			'unit_id' => 'exists:units,id',

        ];
    
    }

    public function product()
	{
		return $this->belongsTo($this->_product(), 'product_id');
	}

	public static function _product()
	{
		return '\\App\Product';
	}

	public function unit()
	{
		return $this->belongsTo($this->_unit(), 'unit_id');
	}

	public static function _unit()
	{
		return '\\App\Unit';
	}

	public static function labelText()
	{
		return ['conversion'];
	}

}
