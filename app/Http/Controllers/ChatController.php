<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Chat;
use App\Models\Broadcast;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use App\Models\Chat as ChatModel;
use App\Http\Requests\ChatSendRequest;

class ChatController extends Controller
{
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->guest()) {
            return abort(403, 'Unauthorized');
        }

        $attributes = $request->validate([
            'text' => 'required|string|min:1',
            'receiver' => 'required'
        ]);

        $receiver = User::findOrFail($attributes['receiver']['id']);

        (new Chat($receiver))->message($attributes['text']);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return ChatModel::where('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(25)
            ->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function edit(ChatModel $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChatModel $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChatModel $chat)
    {
        //
    }
}
