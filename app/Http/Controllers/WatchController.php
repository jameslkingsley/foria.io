<?php

namespace App\Http\Controllers;

use OpenTok\Role;
use App\Models\User;
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use App\Models\Broadcast;
use App\Support\LiveStream;
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
     * Default OpenTok session options.
     *
     * @var array
     */
    protected $sessionOptions = [
        'mediaMode' => MediaMode::ROUTED
    ];

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        // $this->middleware('auth');

        $this->user = $user;

        $this->openTok = new OpenTok(
            config('opentok.key'),
            config('opentok.secret')
        );
    }

    /**
     * Watch the stream.
     *
     * @return mixed
     */
    public function index(Request $request, User $user)
    {
        $broadcast = Broadcast::latest($user);

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

        $token = LiveStream::token(
            $broadcast,
            $broadcast->is_mine
                ? Role::PUBLISHER
                : Role::SUBSCRIBER
        );

        return response()->json(compact(
            'broadcast',
            'token'
        ));
    }
}
