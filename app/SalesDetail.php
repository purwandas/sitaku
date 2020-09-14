<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDetail extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    public static function rule(){
        return [
        	// Define rule here to display data on datatable and generate form builder
            // Example : 'name' => 'required|string|min:8|max:10',
            'qty' => 'string|required',
			'unit_id' => 'exists:units,id',
			'product_id' => 'exists:products,id',
			'sales_id' => 'exists:sales,id',

        ];
    
    }

    public function unit()
	{
		return $this->belongsTo($this->_unit(), 'unit_id');
	}

	public static function _unit()
	{
		return '\\App\Unit';
	}

	public function product()
	{
		return $this->belongsTo($this->_product(), 'product_id');
	}

	public static function _product()
	{
		return '\\App\Product';
	}

	public function sales()
	{
		return $this->belongsTo($this->_sales(), 'sales_id');
	}

	public static function _sales()
	{
		return '\\App\Sales';
	}

	public static function labelText()
	{
		return ['qty'];
	}

}
