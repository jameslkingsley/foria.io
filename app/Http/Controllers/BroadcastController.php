<?php

namespace App\Http\Controllers;

use OpenTok\OpenTok;
use OpenTok\MediaMode;
use App\Models\Broadcast;
use App\Events\TopicChanged;
use Illuminate\Http\Request;
use App\Http\Requests\BroadcastRequest;

class BroadcastController extends Controller
{
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
    public function __construct()
    {
        $this->middleware('auth');

        $this->openTok = new OpenTok(
            config('opentok.api_key'),
            config('opentok.api_secret')
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Start the broadcast
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Stop the broadcast
    }

    /**
     * Starts the broadcast.
     *
     * @return App\Models\Broadcast
     */
    public function start(Request $request)
    {
        $sessionId = $this->openTok
            ->createSession($this->sessionOptions)
            ->getSessionId();

        return Broadcast::start([
            'topic' => $request->topic,
            'session_id' => $sessionId
        ]);
    }

    /**
     * Stops the broadcast.
     *
     * @return mixed
     */
    public function stop(BroadcastRequest $request)
    {
        if (! $request->broadcast()) {
            return;
        }

        $request->broadcast()->stop();
    }

    /**
     * Changes the topic of the broadcast.
     *
     * @return void
     */
    public function topic(BroadcastRequest $request)
    {
        $broadcast = $request->broadcast();

        $broadcast->topic = $request->topic;
        $broadcast->save();

        event(new TopicChanged($request->topic, $broadcast->user));
    }
}
