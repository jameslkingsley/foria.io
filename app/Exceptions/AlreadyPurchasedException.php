<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class AlreadyPurchasedException extends \Exception implements Responsable
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'You have already purchased this item.';

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
