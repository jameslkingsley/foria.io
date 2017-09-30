<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Broadcast;
use Illuminate\Http\Request;

class WatchController extends Controller
{
    /**
     * User model.
     *
     * @var App\Models\User
     */
    protected $user;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        // $this->middleware('auth');

        $this->user = $user;
    }

    /**
     * Watch the stream.
     *
     * @return mixed
     */
    public function index(Request $request, User $user)
    {
        $broadcast = Broadcast::latest($user);

        if (! optional($broadcast)->online && ! $user->is_mine) {
            return redirect($user->profile_url);
        }

        return vue('f-watch', compact('user', 'broadcast'));
    }

    /**
     * Shows the stream viewer and session.
     *
     * @return mixed
     */
    public function show(Request $request, User $user)
    {
        if (! $broadcast = Broadcast::latest($user)) {
            return abort(404);
        }

        return response()->json(compact(
            'broadcast'
        ));
    }
}
