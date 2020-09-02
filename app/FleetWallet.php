<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FleetWallet extends Model
{
    protected $table='fleet_wallet';
    
   	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'fleet_id',        
        'transaction_id',        
        'transaction_alias',
        'transaction_desc',
        'type',
        'amount',
        'open_balance',
        'close_balance',
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
