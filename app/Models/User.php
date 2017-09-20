<?php

namespace App\Models;

use Stripe\Customer;
use App\Traits\Videos;
use App\Traits\Billing;
use App\Traits\Followable;
use App\Traits\Subscriptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Videos,
        Billing,
        Followable,
        Notifiable,
        Subscriptions;

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
        'password',
        'remember_token'
    ];

    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'is_mine',
        'watch_url',
        'has_card_on_file',
        'avatar_url',
        'profile_url',
    ];

    /**
     * Casted attributes.
     *
     * @var array
     */
    protected $casts = [
        'is_model' => 'boolean'
    ];

    /**
     * Get the route key name for Laravel.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

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
     * Gets the profile URL attribute.
     *
     * @return string
     */
    public function getProfileUrlAttribute()
    {
        return url("/profile/{$this->name}");
    }

    /**
     * Gets the avatar URL attribute.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if (starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        return $this->avatar
            ? Storage::url($this->avatar)
            : url('images/placeholder.png');
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
}
