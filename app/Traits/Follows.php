<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Follow;
use App\Events\Followed;
use App\Events\Unfollowed;

trait Follows
{
    /**
     * Gets the follower count attribute.
     *
     * @return integer
     */
    public function getFollowerCountAttribute()
    {
        return $this->followers()->count();
    }

    /**
     * Gets all followers for the model.
     *
     * @return Collection App\Models\Follow
     */
    public function followers()
    {
        return Follow::where('to_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Follows the given user.
     *
     * @return boolean
     */
    public function follow(User $user)
    {
        if ($this->id == $user->id) return false;

        if (! $this->isFollowing($user)) {
            $follow = Follow::create([
                'from_id' => $this->id,
                'to_id' => $user->id
            ]);

            event(new Followed($follow));
        }

        return true;
    }

    /**
     * Un-follows the given user.
     *
     * @return boolean
     */
    public function unfollow(User $user)
    {
        if (! $this->isFollowing($user)) return true;

        $follow = Follow::where('from_id', $this->id)
            ->where('to_id', $user->id)
            ->delete();

        event(new Unfollowed($user));

        return true;
    }

    /**
     * Checks if the given user is being followed.
     *
     * @return boolean
     */
    public function isFollowing(User $user)
    {
        $follow = Follow::where('from_id', $this->id)
            ->where('to_id', $user->id)
            ->first();

        return ! is_null($follow);
    }
}
