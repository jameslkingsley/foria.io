<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the user model.
     *
     * @return App\Models\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gets the latest broadcast for the given user.
     *
     * @return App\Models\Broadcast
     */
    public static function latest($user = null)
    {
        if (! $user) {
            $user = auth()->user();
        }

        return static::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Gets the latest broadcast or a new one.
     *
     * @return App\Models\Broadcast
     */
    public static function latestOrNew($user = null)
    {
        if (! $user) {
            $user = auth()->user();
        }

        $latest = static::latest($user);

        if (! $latest) {
            return static::create([
                'topic' => 'My First Broadcast!',
                'user_id' => $user->id,
                'online' => false
            ]);
        }

        return $latest;
    }

    /**
     * Starts a broadcast for the given session ID.
     *
     * @return App\Models\Broadcast
     */
    public static function start($sessionId)
    {
        return static::create([
            'user_id' => auth()->user()->id,
            'session_id' => $sessionId
        ]);
    }

    /**
     * Gets all online broadcasts, ordered by user preference.
     *
     * @return Collection App\Models\Broadcast
     */
    public static function online()
    {
        // TODO Order by user preference (followed, starred)

        return static::where('online', true)->with('user')->get();
    }
}
