<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRequestLostItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'request_id',
        'parent_id',       
        'user_id',       
        'item_description',        
        'comments',        
        'comments_by',        
        'status',        
        'is_admin',        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'created_at', 'updated_at'
    ];
}
