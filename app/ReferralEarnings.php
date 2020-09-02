<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferralEarnings extends Model
{
    protected $table='referral_earnings';

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'referrer_id',        
        'type',        
        'amount',
        'count', 
        'referral_histroy_id',       
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
