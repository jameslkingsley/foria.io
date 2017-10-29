<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Application extends Model
{
    use BelongsToUser;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the photo ID URL.
     *
     * @return string
     */
    public function getPhotoIdAttribute($value)
    {
        if (is_null($value)) {
            return $value;
        }

        return Storage::cloud()->url($value);
    }

    /**
     * Gets the photo self URL.
     *
     * @return string
     */
    public function getPhotoSelfAttribute($value)
    {
        if (is_null($value)) {
            return $value;
        }

        return Storage::cloud()->url($value);
    }
}
