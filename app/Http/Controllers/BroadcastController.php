<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Support\LiveStream;
use App\Events\TopicChanged;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    /**
     * The live stream instance.
     *
     * @var App\Support\LiveStream
     */
    protected $stream;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(LiveStream $stream)
    {
        $this->middleware('auth');

        $this->stream = $stream;
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
        return auth()->user()->broadcasts()->save(
            new Broadcast([
                'online' => true,
                'topic' => request('topic', 'Untitled')
            ])
        );
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
    public function update(Request $request)
    {
        if (! $broadcast = Broadcast::latest(auth()->user())) {
            return abort(404);
        }

        $broadcast->update([
            'online' => false
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Changes the topic of the broadcast.
     *
     * @return void
     */
    public function topic(Request $request)
    {
        $broadcast = $this->stream->getBroadcast();

        $broadcast->update([
            'topic' => $request->topic
        ]);

        event(new TopicChanged($request->topic, $broadcast->user));
    }
}
