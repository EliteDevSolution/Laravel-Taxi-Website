<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferralHistroy extends Model
{
    protected $table='referral_histroy';

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'referrer_id',        
        'type',        
        'referral_id',
        'referral_data', 
        'status',       
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at'
    ];

}
