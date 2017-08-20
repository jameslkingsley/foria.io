<?php

namespace App\Models;

use Stripe\Customer;
use App\Traits\Follows;
use App\Traits\Purchases;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable,
        Purchases,
        Billable,
        Follows;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

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
        'watch_url',
        'has_card_on_file'
    ];

    /**
     * Checks whether the user has a card on file.
     *
     * @return boolean
     */
    public function getHasCardOnFileAttribute()
    {
        return $this->hasCardOnFile();
    }

    /**
     * Gets the is_mine attribute.
     *
     * @return boolean
     */
    public function getIsMineAttribute()
    {
        if (auth()->guest()) {
            return false;
        }

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
    public function stripeCustomer($token = null)
    {
        if (! $this->stripe_id) {
            if ($token) {
                $customer = Customer::create([
                    'email' => $this->email,
                    'source' => $token
                ]);

                $this->stripe_id = $customer->id;
                $this->save();

                return $this->stripeCustomer();
            }

            return null;
        }

        return Customer::retrieve($this->stripe_id);
    }
}
