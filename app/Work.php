<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'age', 'work'
    ];
}
