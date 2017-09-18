<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Follow;
use App\Events\Followed;
use App\Events\Unfollowed;

trait Followable
{
    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->appends[] = 'follower_count';
    }

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
     * Gets the followers for the model.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function followers()
    {
        return $this->morphMany(Follow::class, 'model')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Follows the given user.
     *
     * @return App\Models\Follow
     */
    public function follow(User $user)
    {
        if ($this->id == $user->id) {
            throw new InvalidArgumentException;
        }

        if (! $this->isFollowing($user)) {
            $follow = $user->followers()->save(
                new Follow([
                    'user_id' => $this->id
                ])
            );

            event(new Followed($follow));

            return $follow;
        }

        throw new InvalidArgumentException;
    }

    /**
     * Un-follows the given user.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function unfollow(User $user)
    {
        if (! $this->isFollowing($user)) {
            throw new InvalidArgumentException;
        }

        $user->followers()
            ->where('user_id', $this->id)
            ->delete();

        event(new Unfollowed($user));

        return $this;
    }

    /**
     * Determine if the given user is being followed.
     *
     * @return boolean
     */
    public function isFollowing(User $user)
    {
        return $user->followers()
            ->where('user_id', $this->id)
            ->exists();
    }
}
