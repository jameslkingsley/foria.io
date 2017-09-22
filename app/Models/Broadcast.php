<?php

namespace App\Models;

use App\Models\User;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use BelongsToUser;

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
        return ! auth()->guest() && $this->user_id == auth()->user()->id;
    }
}
