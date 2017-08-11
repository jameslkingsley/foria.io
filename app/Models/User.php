<?php

namespace App\Models;

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
        'follower_count'
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
}
