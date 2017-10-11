<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class VideoTooShortException extends \Exception implements Responsable
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'Video duration must be more than 60 seconds.';

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
