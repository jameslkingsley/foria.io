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
        return Video::where('user_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
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
