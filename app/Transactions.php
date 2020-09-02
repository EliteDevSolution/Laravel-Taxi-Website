<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
	/**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'transaction_type', 'invoice_id', 'user_id', 'provider_id', 'admin_id', 'fleet_id', 'tax_per', 'tax_amount','commison_per','commision_amount', 'fleet_per','fleet_commission','discount_per','discount_amount','tips','total_amount','payment_mode','payment_transaction_id','is_partial','wallet_amount','online_amount','cash_amount','status'
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
