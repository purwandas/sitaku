<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    public static function rule(){
        return [
        	// Define rule here to display data on datatable and generate form builder
            // Example : 'name' => 'required|string|min:8|max:10',
            'name' => 'string|required',
			'address' => 'string|required',
			'phone' => 'string|required',
			'production_id' => 'exists:productions,id',

        ];
    
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
