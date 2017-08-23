<?php

namespace App\Models;

use Stripe\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the 'from' user model.
     *
     * @return App\Models\User
     */
    public function from()
    {
        return $this->hasOne(User::class, 'id', 'from_id');
    }

    /**
     * Gets the 'to' user model.
     *
     * @return App\Models\User
     */
    public function to()
    {
        return $this->hasOne(User::class, 'id', 'to_id');
    }

    /**
     * Gets all Stripe subscription plans.
     *
     * @return Collection
     */
    public static function plans()
    {
        return collect(Plan::all()->data)->sortBy('amount')->values()->all();
    }
}
