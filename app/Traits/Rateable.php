<?php

namespace App\Traits;

use App\Models\Rating;
use Illuminate\Auth\AuthenticationException;

trait Rateable
{
    /**
     * Gets the ratings for the resource.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'model');
    }

    /**
     * Gets the ratings compacted for JSON responses.
     *
     * @return array
     */
    public function ratingsCompact()
    {
        $ratings = $this->ratings()->get(['type'])->groupBy('type');

        return [
            'likes' => $ratings->get('like', collect())->count(),
            'dislikes' => $ratings->get('dislike', collect())->count(),
            'has_liked' => $this->hasLiked(),
            'has_disliked' => $this->hasDisliked()
        ];
    }

    /**
     * Gets the user's rating for the resource.
     *
     * @return App\Models\Rating
     */
    public function rating()
    {
        return $this->ratings()
            ->where('user_id', auth()->user()->id)
            ->first();
    }

    /**
     * Determines if the user has liked the resource.
     *
     * @return boolean
     */
    public function hasLiked()
    {
        if (auth()->guest()) {
            return false;
        }

        return $this->ratings()
            ->where('user_id', auth()->user()->id)
            ->where('type', 'like')
            ->exists();
    }

    /**
     * Determines if the user has disliked the resource.
     *
     * @return boolean
     */
    public function hasDisliked()
    {
        if (auth()->guest()) {
            return false;
        }

        return $this->ratings()
            ->where('user_id', auth()->user()->id)
            ->where('type', 'dislike')
            ->exists();
    }

    /**
     * Rates the resource as the authenticated user.
     *
     * @return App\Models\Rating
     */
    public function rate($type)
    {
        if (auth()->guest()) {
            throw new AuthenticationException;
        }

        if (! str_contains((string) $type, ['like', 'dislike'])) {
            throw new InvalidArgumentException;
        }

        return $this->ratings()->save(
            new Rating([
                'user_id' => auth()->user()->id,
                'type' => $type
            ])
        );
    }

    /**
     * Deletes the rating for the resource.
     *
     * @return App\Models\Rating
     */
    public function unrate()
    {
        return tap(optional($this->rating()), function ($rating) {
            $rating->delete();
        });
    }
}
