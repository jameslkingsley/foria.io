<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Events\TopicChanged;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'model']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $attributes = $request->validate([
            'topic' => 'required|string|min:1'
        ]);

        auth()->user()->update($attributes);

        if ($broadcast = Broadcast::latest()) {
            $broadcast->update($attributes);

            event(new TopicChanged($broadcast));
        }
    }
}
