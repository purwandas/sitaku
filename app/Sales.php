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
			'user_id' => 'exists:users,id',
			'date'    => 'date',
			'total_payment' => 'numeric',
			'total_paid'    => 'numeric',
			'total_change'  => 'numeric',

        ];
    
    }

	public function sales_details()
	{
		return $this->hasMany(SalesDetail::class);
	}

    public function user()
	{
		return $this->belongsTo($this->_user(), 'user_id');
	}

	public static function _user()
	{
		return '\\App\User';
	}

	public function product()
	{
		return $this->sales_details()->product();
	}

	public static function _product()
	{
		return '\\App\Product';
	}

	public static function labelText()
	{
		return ['date'];
	}

}
