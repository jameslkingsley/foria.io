<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Aws\S3\MultipartUploader;
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
        return $user->videos();
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
        $destination = 'videos/'.$name.'.'.$file->guessClientExtension();

        $client = Storage::disk('s3')->getDriver()->getAdapter()->getClient();

        $uploader = new MultipartUploader($client, $source, [
            'bucket' => config('filesystems.disks.s3.bucket'),
            'key' => $destination,
        ]);

        do {
            try {
                $result = $uploader->upload();
                $transcodedPath = 'videos/transcoded/'.$name.'.mp4';

                // Start transcoder service (convert to MP4)
                $transcode = $transcoder->createJob([
                    'PipelineId' => '1504112665437-qnedrs',
                    'Input' => ['Key' => $destination],
                    'Output' => [
                        'Key' => $transcodedPath,
                        'PresetId' => '1351620000001-000001'
                    ]
                ])->toArray();

                // Create video record
                $video = Video::create([
                    'user_id' => auth()->user()->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $transcodedPath,
                    'transcoder_id' => $transcode['Job']['Id'],
                    'length' => 0,
                    'width' => 0,
                    'height' => 0,
                ]);
            } catch (MultipartUploadException $e) {
                rewind($source);

                $uploader = new MultipartUploader($client, $source, [
                    'state' => $e->getState(),
                ]);
            }
        } while (! isset($result));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video/*, CloudFrontClient $cloudFront*/)
    {
        // $url = config('services.cloudfront.url');

        // $signedUrlCannedPolicy = $cloudFront->getSignedUrl([
        //     'url' => "{$url}/{$video->path}",
        //     'expires' => time() + 300,
        //     'private_key' => config('services.cloudfront.private_key'),
        //     'key_pair_id' => config('services.cloudfront.access_key')
        // ]);

        // $manifestUrl = "";// "{$url}/cfx/st/&mp4:{$signedUrlCannedPolicy}";
        $videoUrl = Storage::url($video->path);

        return vue('f-video', compact('video', 'videoUrl'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        //
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
        //
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
