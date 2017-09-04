<?php

namespace App\Traits;

use App\Models\User;

trait ResolveTo
{
    /**
     * Resolves the to_id to a user instance.
     *
     * @return App\Models\User|null
     */
    public function to()
    {
        return $this->hasOne(User::class, 'id', 'to_id');
    }
}
