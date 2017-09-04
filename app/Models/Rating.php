<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use BelongsToUser;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the rateable model.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function rateable()
    {
        return $this->morphTo();
    }

    /**
     * Resolves the morphed model class.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public static function getModel($name)
    {
        $name = 'App\\Models\\'.studly_case($name);

        return new $name;
    }

    /**
     * Retrieves the model resource.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public static function resolveModel($name, $id)
    {
        return static::getModel($name)->findOrFail($id);
    }
}
