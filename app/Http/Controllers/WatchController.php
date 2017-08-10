<?php

namespace App\Http\Controllers;

use App\User;
use OpenTok\Role;
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use App\Models\Session;
use Illuminate\Http\Request;

class WatchController extends Controller
{
    /**
     * User model.
     *
     * @var App\User
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

        return view('watch.index', compact('user'));
    }

    /**
     * Shows the stream viewer and session.
     *
     * @return mixed
     */
    public function show(Request $request, User $user)
    {
        if (! $session = Session::latestFor($user)) {
            return abort(403);
        }

        $sessionId = $session->session_id;
        $apiKey = config('opentok.api_key');
        $role = (!auth()->guest() && $user->id == auth()->user()->id) ? Role::PUBLISHER : Role::SUBSCRIBER;

        $token = $this->openTok->generateToken(
            $sessionId,
            ['role' => $role]
        );

        return response()->json(compact(
            'user',
            'apiKey',
            'sessionId',
            'token',
            'role'
        ));
    }

    /**
     * Starts the streaming session.
     *
     * @return void
     */
    public function start()
    {
        $sessionId = $this->openTok
            ->createSession($this->sessionOptions)
            ->getSessionId();

        Session::start($sessionId);

        return $sessionId;
    }
}
