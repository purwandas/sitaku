<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    public static function rule(){
        return [
        	// Define rule here to display data on datatable and generate form builder
            // Example : 'name' => 'required|string|min:8|max:10',
            'user_id' => 'exists:users,id',
			'supplier_id' => 'exists:suppliers,id',
			'date' => 'date|required',
			'total_payment' => 'numeric',
			'total_paid' => 'numeric',
			'total_change' => 'numeric',

        ];
    
    }

    public function user()
	{
		return $this->belongsTo($this->_user(), 'user_id');
	}

	public static function _user()
	{
		return '\\App\User';
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
		return ['user_id'];
	}

}
