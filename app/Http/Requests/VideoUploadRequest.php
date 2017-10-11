<?php

namespace App\Http\Requests;

use FFMpeg\FFMpeg;
use App\Jobs\ProcessVideo;
use FFMpeg\Format\Video as Format;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;

class VideoUploadRequest extends FormRequest
{
    /**
     * The key name of the video.
     *
     * @var string
     */
    protected $key;

    /**
     * Destination directory path.
     *
     * @var string
     */
    protected $destination;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct()
    {
        $this->key = md5(microtime());

        $this->destination = (object) [
            'directory' => "videos/{$this->key}",
            'unprocessed' => "app/videos/{$this->key}/unprocessed.",
            'processed' => "app/videos/{$this->key}/processed.mp4",
            'thumbnails' => "app/videos/{$this->key}/thumbnails"
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check()
            && auth()->user()->is_model;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'video' => ['required']
        ];
    }

    /**
     * Handles the video upload request.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->upload();
    }

    /**
     * Uploads the video file.
     *
     * @return mixed
     */
    protected function upload()
    {
        $file = $this->file('video');

        $source = fopen(
            $file->getRealPath(),
            'r+'
        );

        $destination = $this->destination->unprocessed . $file->guessClientExtension();

        Storage::disk('root')
            ->put($destination, $source, 'private');

        ProcessVideo::dispatch($this->destination, storage_path($destination));
    }

    /**
     * Collects the meta data such as a preview video and thumbnails.
     *
     * @return mixed
     */
    protected function collect()
    {
        \Log::info('File exists? ' . file_exists(storage_path($this->destination->processed)));

        $video = FFMpeg::create()
            ->open(storage_path($this->destination->processed));

        $duration = $video->getStreams()
            ->videos()
            ->first()
            ->get('duration');

        // Create thumbnails directory
        mkdir(storage_path($this->destination->thumbnails));

        $video
            ->filters()
            ->extractMultipleFrames(
                ExtractMultipleFramesFilter::FRAMERATE_EVERY_60SEC,
                storage_path($this->destination->thumbnails)
            )
            ->synchronize();

        $this->saveEncoding($video);
    }

    /**
     * Deploy the converted video to cloud storage.
     *
     * @return mixed
     */
    protected function deploy()
    {
        //
    }
}
