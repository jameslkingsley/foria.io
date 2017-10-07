<?php

namespace App\Http\Controllers;

use Aws\Command;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Aws\S3\MultipartUploader;
use App\Events\UploadProgress;
use App\Jobs\TranscoderStatus;
use Aws\CloudFront\CloudFrontClient;
use Illuminate\Support\Facades\Storage;
use Aws\Exception\MultipartUploadException;
use Aws\ElasticTranscoder\ElasticTranscoderClient;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ElasticTranscoderClient $transcoder)
    {
        // Validate file is video
        $request->validate([
            'video' => ['required'] // TODO Check if video
        ]);

        // Upload video to AWS S3
        $file = $request->file('video');
        $source = fopen($file->getRealPath(), 'r+');
        $name = md5(microtime());
        $destination = "videos/$name/$name.{$file->guessClientExtension()}";

        $client = Storage::cloud()->getDriver()->getAdapter()->getClient();

        $client->putObject([
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $destination,
            'SourceFile' => $file,
            '@http' => [
                'progress' => function ($downloadTotalSize, $downloadSizeSoFar, $uploadTotalSize, $uploadSizeSoFar) {
                    $lastEmit = session('lastUploadProgressTime', null);

                    if (is_null($lastEmit) || (time() - $lastEmit) >= 1) {
                        // Emit progress data
                        event(new UploadProgress(auth()->user(), $uploadSizeSoFar, $uploadTotalSize));

                        // Store emit time in session
                        session(['lastUploadProgressTime' => time()]);
                    }
                }
            ]
        ]);

        $transcodedPath = "videos/$name/transcoded/$name.mp4";

        // Create the transcoder job (convert to MP4)
        $transcode = $transcoder->createJob([
            'PipelineId' => config('services.aws.ets.pipeline_id'),
            'Input' => ['Key' => $destination],
            'Output' => [
                'Key' => $transcodedPath,
                'PresetId' => config('services.aws.ets.preset_id'),
                'ThumbnailPattern' => "videos/$name/thumbnails/{count}"
            ]
        ])->toArray();

        // Create video record
        $video = Video::create([
            'user_id' => auth()->user()->id,
            'name' => $file->getClientOriginalName(),
            'key' => $name,
            'path' => $transcodedPath,
            'transcoder_id' => $transcode['Job']['Id']
        ]);

        // Dispatch the status job to update the processing
        // status once the transcoder has completed
        TranscoderStatus::dispatch(auth()->user(), $video);

        return $video;
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
            'token_price' => ['nullable', 'numeric'],
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
