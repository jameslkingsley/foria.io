<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
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
        'url',
        'stream_url',
        'edit_url',
        'is_mine'
    ];

    /**
     * Casted attributes.
     *
     * @var array
     */
    protected $casts = [
        'subscriber_only' => 'boolean'
    ];

    /**
     * Gets the watch URL for this video.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url("/videos/{$this->id}");
    }

    /**
     * Determines if the video belongs to the authenticated user.
     *
     * @return boolean
     */
    public function getIsMineAttribute()
    {
        if (auth()->guest()) {
            return false;
        }

        return $this->user_id == auth()->user()->id;
    }

    /**
     * Gets the edit URL for this video.
     *
     * @return string
     */
    public function getEditUrlAttribute()
    {
        return url("/videos/edit/{$this->id}");
    }

    /**
     * Gets the stream URL for this video.
     *
     * @return string
     */
    public function getStreamUrlAttribute()
    {
        if (! is_null($this->path)) {
            return Storage::url($this->path);
        }

        return null;
    }
}
