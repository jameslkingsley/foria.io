<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class PrivacyException extends \Exception implements Responsable
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'The privacy settings prohibit this action.';

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
