<?php

namespace App\Models;

use App\Traits\Rateable;
use App\Traits\Reportable;
use App\Traits\Commentable;
use App\Traits\Purchasable;
use App\Contracts\Purchase;
use App\Traits\BelongsToUser;
use App\Traits\Referenceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model implements Purchase
{
    use Rateable,
        Reportable,
        Purchasable,
        Commentable,
        BelongsToUser,
        Referenceable;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'user_id'
    ];

    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'url',
        'stream_url',
        'edit_url',
        'is_mine',
        'thumbnails',
        'thumbnail',
        'unlocked',
        'locked',
    ];

    /**
     * Gets the purchasable details.
     *
     * @return array
     */
    public function getPurchaseDetails()
    {
        return [
            'name' => $this->name,
            'amount' => $this->token_price
        ];
    }

    /**
     * Determines if the video is locked to the authenticated user.
     *
     * @return boolean
     */
    public function getLockedAttribute()
    {
        if (auth()->guest()) {
            return true;
        }

        if ($this->required_subscription) {
            return ! auth()->user()->subscribedTo($this->user);
        }

        return ! $this->purchased();
    }

    /**
     * Determines if the video is unlocked to the authenticated user.
     *
     * @return boolean
     */
    public function getUnlockedAttribute()
    {
        return ! $this->locked;
    }

    /**
     * Gets the bucket directory path for the video.
     *
     * @return string
     */
    public function getPath(string $uri = '')
    {
        return "videos/{$this->key}/$uri";
    }

    /**
     * Gets all thumbnails for this video.
     *
     * @return array
     */
    public function getThumbnailsAttribute()
    {
        $files = Storage::files($this->getPath('thumbnails'));

        return collect($files)->sort()->transform(function ($file) {
            return Storage::url($file);
        });
    }

    /**
     * Gets the thumbnail for this video.
     *
     * @return string
     */
    public function getThumbnailAttribute()
    {
        $count = $this->thumbnails->count();

        if ($count <= 1) {
            return $this->thumbnails->first();
        }

        return $this->thumbnails[floor($count / 2)];
    }

    /**
     * Gets the watch URL for this video.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url("/videos/{$this->ref}");
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
        return url("/videos/edit/{$this->ref}");
    }

    /**
     * Determines if the video can be viewed by the authenticated user.
     *
     * @return boolean
     */
    public function viewable()
    {
        if ($this->is_mine) {
            return true;
        }

        if ($this->privacy == 'private') {
            return false;
        }

        if ($this->required_subscription) {
            if (auth()->guest() || ! auth()->user()->subscribedTo($this->user)) {
                return false;
            }
        }

        if ($this->token_price) {
            return $this->purchased();
        }

        return true;
    }

    /**
     * Gets the stream URL for this video.
     *
     * @return string
     */
    public function getStreamUrlAttribute()
    {
        if (! $this->viewable()) {
            return null;
        }

        if (! is_null($this->path)) {
            if (starts_with($this->path, 'http')) {
                return $this->path;
            } else {
                return Storage::url($this->path);
            }
        }

        return null;
    }
}
