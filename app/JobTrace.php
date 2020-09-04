<?php

namespace App;

use App\BaseModel;
use App\User;

class JobTrace extends BaseModel
{
    /**
     * STSTIC CONSTST
     */
	const STATUS_PROCESS    = "PROCESSING";
	const STATUS_SUCCESS    = "SUCCESS";
    const STATUS_FAILED     = "FAILED";
    const STATUS_SUSPEND    = "SUSPENDED";

    protected $guarded = [];

    public static function boot()	
	{
        parent::boot();
	}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}