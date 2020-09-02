<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderCard extends Model
{

	protected $fillable = ['user_id','last_four','card_id','brand','is_default'];
    
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
