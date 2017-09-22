<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class NoBroadcastException extends \Exception implements Responsable
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'No broadcast exists on the instance or in the session.';

    /**
     * Returns the response.
     *
     * @return string
     */
    public function toResponse($request)
    {
        return $this->message;
    }
}
