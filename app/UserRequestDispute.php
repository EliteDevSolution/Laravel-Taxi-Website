<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRequestDispute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'request_id',
        'dispute_type',
        'user_id',
        'provider_id',
        'dispute_name',        
        'comments',        
        'refund_amount',        
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

    public function request()
    {
        return $this->belongsTo('App\UserRequests');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function provider()
    {
        return $this->belongsTo('App\Provider');
    }
}
