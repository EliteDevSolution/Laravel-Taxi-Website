<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeakHour extends Model
{
    protected $table='peak_hours'; 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_time','end_time'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at'
    ];

        
    public function servicetimes()
    {
        return $this->hasOne('App\ServicePeakHour', 'peak_hours_id')->select(array('min_price', 'peak_hours_id'));
    }    

}
