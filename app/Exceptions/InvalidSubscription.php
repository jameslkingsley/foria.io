<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class InvalidSubscription extends \Exception implements Responsable
{
    /**
     * Returns the response.
     *
     * @return string
     */
    public function toResponse($request)
    {
        return 'Cannot subscribe to model.';
    }
}
