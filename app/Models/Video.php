<?php

namespace App\Models;

use App\Traits\Rateable;
use App\Traits\Reportable;
use App\Contracts\Purchase;
use App\Traits\Commentable;
use App\Traits\Purchasable;
use App\Traits\BelongsToUser;
use App\Traits\Referenceable;
use Illuminate\Support\Facades\Cache;
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
            'payee' => $this->user->id,
            'amount' => $this->token_price,
            'once' => true, // Can only purchase a video once
            'allowed' => $this->privacy === 'public'
                && !$this->user->is_mine,
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

        if ($this->is_mine) {
            return false;
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
    public function getThumbnailsAttribute($value)
    {
        return collect(json_decode($value));
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
     * Determines if the video can be previewed.
     *
     * @return boolean
     */
    public function previewable()
    {
        if ($this->is_mine) {
            return true;
        }

        if ($this->privacy == 'private') {
            return false;
        }

        return true;
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

        return false;
    }

    /**
     * Gets the stream URL for this video.
     *
     * @return string
     */
    public function getStreamUrlAttribute()
    {
        if (! $this->viewable()) {
            if ($this->previewable()) {
                return Storage::cloud()->url("videos/{$this->key}/preview.mp4");
            }

            return null;
        }

        return Storage::cloud()->url("videos/{$this->key}/processed.mp4");

        return null;
    }

    /**
     * Gets a collection of recent videos.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function recent()
    {
        return Cache::remember('videos-trending', 60, function () {
            return static::wherePrivacy('public')
                ->whereHasProcessed(true)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->take(12)
                ->get();
        });
    }

    /**
     * Gets a collection of trending videos.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function trending()
    {
        // TODO Get the most viewed videos this week

        return collect();
    }

    /**
     * Gets a collection of the authenticated user's feed.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function feed()
    {
        if (auth()->guest()) {
            return collect();
        }

        return Cache::remember('videos-feed', 60, function () {
            return auth()->user()->follows()->transform(function ($follow) {
                return $follow->model->videos()
                    ->where('created_at', '>=', now()->subMonth())
                    ->with('user')
                    ->take(6)
                    ->get();
            })->collapse();
        });
    }
}
