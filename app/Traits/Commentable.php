<?php

namespace App\Traits;

use App\Models\Comment;
use Illuminate\Auth\AuthenticationException;

trait Commentable
{
    /**
     * Gets the comments for the model.
     *
     * @return Illuminate\Database\Eloquent\QueryBuilder
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'model')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Makes a new comment on the model.
     *
     * @return App\Models\Comment
     */
    public function comment(string $body)
    {
        if (auth()->guest()) {
            throw new AuthenticationException;
        }

        return $this->comments()->save(
            new Comment([
                'user_id' => auth()->user()->id,
                'body' => $body
            ])
        );
    }
}
