<?php

namespace App\Http\Controllers;

use OpenTok\Role;
use OpenTok\OpenTok;
use App\Models\User;
use OpenTok\MediaMode;
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
            config('opentok.api_key'),
            config('opentok.api_secret')
        );
    }

    /**
     * Watch the stream.
     *
     * @return mixed
     */
    public function index(Request $request, string $name)
    {
        $user = $this->user->where('name', $name)->first();

        if (! $user) {
            return abort(404);
        }

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
            return abort(403);
        }

        $role = $broadcast->is_mine ? Role::PUBLISHER : Role::SUBSCRIBER;

        $token = $this->openTok->generateToken(
            $broadcast->session_id,
            ['role' => $role]
        );

        return response()->json(compact(
            'broadcast',
            'token'
        ));
    }

    /**
     * Starts the streaming session.
     *
     * @return void
     */
    public function start(Request $request, User $user)
    {
        if (auth()->guest() || $user->id != auth()->user()->id) {
            return abort(403);
        }

        $sessionId = $this->openTok
            ->createSession($this->sessionOptions)
            ->getSessionId();

        Broadcast::start($sessionId);

        return $sessionId;
    }
}
