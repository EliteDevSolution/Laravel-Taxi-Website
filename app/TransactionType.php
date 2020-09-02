<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
	/**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'type_name', 'type_desc', 'type_ref', 'type_start'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at'
    ];
}
