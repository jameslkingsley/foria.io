<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Returns whether the user is being followed.
     *
     * @return boolean
     */
    public function index(Request $request, User $to)
    {
        if (auth()->guest()) return false;

        return response()->json(auth()->user()->isFollowing($to));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $to)
    {
        if (auth()->guest()) return false;

        return response()->json(auth()->user()->follow($to));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $to)
    {
        if (auth()->guest()) return false;

        return response()->json(auth()->user()->unfollow($to));
    }
}
