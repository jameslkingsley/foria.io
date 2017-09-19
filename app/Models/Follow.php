<?php

namespace App\Models;

use App\Models\User;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use BelongsToUser;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the followable model.
     *
     * @return Illuminate\Database\Eloquent\QueryBuilder
     */
    public function model()
    {
        return $this->morphTo();
    }
}
