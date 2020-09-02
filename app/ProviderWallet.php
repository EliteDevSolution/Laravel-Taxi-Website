<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderWallet extends Model
{
    protected $table='provider_wallet';

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'provider_id',        
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

    public function transactions()
    {
        return $this->hasMany('App\ProviderWallet', 'transaction_alias','transaction_alias');
    }
}
