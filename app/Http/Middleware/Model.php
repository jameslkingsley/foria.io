<?php

namespace App\Http\Middleware;

use Closure;

class Model
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->guest()) {
            return abort(403);
        }

        if (! auth()->user()->is_model) {
            return abort(403);
        }

        return $next($request);
    }
}
