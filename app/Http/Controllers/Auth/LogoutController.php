<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    /**
     * Logs out the user, redirects to home.
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        auth()->logout();

        return redirect('/');
    }
}
