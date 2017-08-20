<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the user model.
     *
     * @return App\Models\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
