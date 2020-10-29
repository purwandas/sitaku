<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDetail extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    public static function rule(){
        return [
        	// Define rule here to display data on datatable and generate form builder
            // Example : 'name' => 'required|string|min:8|max:10',
            'qty' => 'numeric|required',
            'price' => 'numeric|required',
            'total' => 'numeric|required',
			'unit_id' => 'exists:units,id|required',
			'product_id' => 'exists:products,id|required',
			'purchase_id' => 'exists:purchases,id|required',

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

	public function purchase()
	{
		return $this->belongsTo($this->_purchase(), 'purchase_id');
	}

	public static function _purchase()
	{
		return '\\App\Purchase';
	}

	public static function labelText()
	{
		return ['qty'];
	}

}
