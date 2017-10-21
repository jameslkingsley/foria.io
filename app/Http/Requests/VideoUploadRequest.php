<?php

namespace App\Http\Requests;

use FFMpeg\FFMpeg;
use App\Jobs\ProcessVideo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
{
    protected $redirect = '/test';

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
            'key' => $this->key,
            'directory' => "app/videos/{$this->key}",
            'unprocessed' => "app/videos/{$this->key}/unprocessed.",
            'processed' => "app/videos/{$this->key}/processed.mp4",
            'preview' => "app/videos/{$this->key}/preview.mp4",
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
            // 'video' => ['required']
        ];
    }

    /**
     * Uploads the video file.
     *
     * @return object
     */
    public function upload()
    {
        $file = $this->file('video');

        $source = fopen(
            $file->getRealPath(),
            'r+'
        );

        $destination = $this->destination->unprocessed . $file->getClientOriginalExtension();

        Storage::disk('root')
            ->put($destination, $source, 'private');

        ProcessVideo::dispatch(
            $this->destination,
            storage_path($destination),
            $file->getClientOriginalName(),
            auth()->user()
        );

        return $this->destination;
    }
}
