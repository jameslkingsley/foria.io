<?php

namespace App\Traits;

use App\Models\Video;

trait Videos
{
    /**
     * Gets the videos for the user.
     *
     * @return Collection App\Models\Video
     */
    public function videos()
    {
        $query = $this->hasMany(Video::class)
            ->orderBy('created_at', 'desc');

        if (! $this->is_mine) {
            $query->where('privacy', 'public');
        }

        return $query;
    }

    /**
     * Determines if the user purchased the given video.
     *
     * @return boolean
     */
    public function purchasedVideo(Video $video)
    {
        return true; // TODO
    }
}
