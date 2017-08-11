<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the latest session for the given user.
     *
     * @return App\Models\Session
     */
    public static function latestFor(User $user)
    {
        return static::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Starts a session for the given session ID.
     *
     * @return App\Models\Session
     */
    public static function start($sessionId)
    {
        return static::create([
            'user_id' => auth()->user()->id,
            'session_id' => $sessionId
        ]);
    }
}
