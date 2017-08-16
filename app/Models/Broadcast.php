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
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'is_mine'
    ];

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
            ->where('online', true)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Starts a broadcast for the given session ID.
     *
     * @return App\Models\Broadcast
     */
    public static function start(array $attributes)
    {
        $attributes = array_merge($attributes, [
            'user_id' => auth()->user()->id,
            'online' => true
        ]);

        $broadcast = static::create($attributes);

        session(['broadcast' => $broadcast]);

        return $broadcast;
    }

    /**
     * Stops the broadcast.
     *
     * @return void
     */
    public function stop()
    {
        $this->online = false;
        $this->save();

        session(['broadcast' => null]);
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

    /**
     * Checks whether the broadcast belongs to the authenticated user.
     *
     * @return boolean
     */
    public function getIsMineAttribute()
    {
        return !auth()->guest() && $this->user_id == auth()->user()->id;
    }
}
