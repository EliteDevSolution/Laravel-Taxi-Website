<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'endpoint',
        'public_key',
        'auth_token',
        'admin_id',
        'user_id',
        'provider_id',
        'dispatcher_id',
    ];

    /**
     * Get the user that owns the subscription.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function user()
    {
        return $this->belongsTo(Config::get('auth.providers.users.model'));
    }

    /**
     * Find a subscription by the given endpint.
     *
     * @param  string $endpoint
     * @return static|null
     */
    public static function findByEndpoint($endpoint)
    {
        return static::where('endpoint', $endpoint)->first();
    }
}
