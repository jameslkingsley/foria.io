<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class NoCustomerException extends \Exception implements Responsable
{
    /**
     * Returns the response.
     *
     * @return string
     */
    public function toResponse($request)
    {
        return 'Could not find the Stripe customer.';
    }
}
