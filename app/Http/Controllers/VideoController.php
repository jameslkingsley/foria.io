<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\VideoUploadRequest;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        return $user->videos;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return vue('f-video-upload');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\VideoUploadRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VideoUploadRequest $request)
    {
        try {
            return response()->json(
                $request->upload()
            );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return abort(400, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        if (! $video->previewable() && ! $video->viewable()) {
            return abort(404);
        }

        $video->load('user', 'comments.user');

        return vue('f-video', compact('video'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function showJson(Video $video)
    {
        return $video->load('user');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        return vue('f-video-edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'min:1'],
            'privacy' => ['required', 'string', 'in:public,private'],
            'token_price' => ['nullable', 'numeric'], // TODO Add minimum
            'required_subscription' => ['nullable', 'string', 'in:bronze,silver,gold'],
        ]);

        $video->update($attributes);

        return response('', 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        //
    }
}
