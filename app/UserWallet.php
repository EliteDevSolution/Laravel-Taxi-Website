<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    protected $table='user_wallet';

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'user_id',        
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
