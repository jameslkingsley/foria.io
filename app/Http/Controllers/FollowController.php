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
    public function index(Request $request, User $user)
    {
        if (auth()->guest()) {
            return false;
        }

        return response()->json(
            auth()->user()->isFollowing($user)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        if (auth()->guest()) {
            return false;
        }

        return response()->json(
            auth()->user()->follow($user)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        if (auth()->guest()) {
            return false;
        }

        return response()->json(
            auth()->user()->unfollow($user)
        );
    }
}
