<?php

namespace App\Models;

use Stripe\Customer;
use App\Traits\Follows;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable,
        Follows;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'is_mine',
        'follower_count',
        'watch_url'
    ];

    /**
     * Gets the is_mine attribute.
     *
     * @return boolean
     */
    public function getIsMineAttribute()
    {
        if (auth()->guest()) return false;

        return $this->id == auth()->user()->id;
    }

    /**
     * Gets the watch URL attribute.
     *
     * @return string
     */
    public function getWatchUrlAttribute()
    {
        return url("/watch/{$this->name}");
    }

    /**
     * Gets the Stripe customer for the user.
     *
     * @return Stripe\Customer
     */
    public function stripeCustomer()
    {
        if (! $this->stripe_customer_id) {
            return null;
        }

        return Customer::retrieve($this->stripe_customer_id);
    }
}
