<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    public static function rule(){
        return [
        	// Define rule here to display data on datatable and generate form builder
            // Example : 'name' => 'required|string|min:8|max:10',
            'name' => 'string|required',
			'stock' => 'regex:/^\d+(\.\d{1,2})?$/',
			'buying_price' => 'numeric',
			'selling_price' => 'numeric|nullable',
			'unit_id' => 'exists:units,id|nullable',
			'category_id' => 'exists:categories,id',
			'production_id' => 'exists:productions,id',

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

    public function category()
	{
		return $this->belongsTo($this->_category(), 'category_id');
	}

	public static function _category()
	{
		return '\\App\Category';
	}

	public function production()
	{
		return $this->belongsTo($this->_production(), 'production_id');
	}

	public static function _production()
	{
		return '\\App\Production';
	}

	public static function labelText()
	{
		return ['name'];
	}

}
