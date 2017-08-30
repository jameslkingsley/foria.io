<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

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
        'url'
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
}
