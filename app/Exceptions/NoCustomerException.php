<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class NoCustomerException extends \Exception implements Responsable
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'Could not find the Stripe customer.';

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
