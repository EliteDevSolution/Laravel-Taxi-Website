<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminWallet extends Model
{
    protected $table='admin_wallet';
    
	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
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
