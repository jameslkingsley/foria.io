<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
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
