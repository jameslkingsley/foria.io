<?php

namespace App\Traits;

use App\Models\User;

trait ResolveFrom
{
    /**
     * Resolves the from_id to a user instance.
     *
     * @return App\Models\User|null
     */
    public function from()
    {
        return $this->hasOne(User::class, 'id', 'from_id');
    }
}
