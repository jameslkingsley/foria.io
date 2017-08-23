<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class InvalidSubscriptionException extends \Exception implements Responsable
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

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception.
     *
     * @param  \Illuminate\Http\Request
     * @return void
     */
    public function render($request)
    {
        return response('Cannot subscribe to model');
    }
}
