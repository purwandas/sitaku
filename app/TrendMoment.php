<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrendMoment extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];

    public static function rule(){
        return [
        	// Define rule here to display data on datatable and generate form builder
            // Example : 'name' => 'required|string|min:8|max:10',
            'month_' => 'numeric',
			'year_' => 'numeric',
			'total_sales' => 'numeric',
            'product_id' => 'exists:products,id',

        ];
    
    }

    public static function monthArray()
    {
        return [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    public static function labelText()
	{
		return ['month_'];
	}

}
