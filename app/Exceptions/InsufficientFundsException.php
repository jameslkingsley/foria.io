<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class InsufficientFundsException extends \Exception implements Responsable
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'You do not have enough tokens.';

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
