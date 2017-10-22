<?php

namespace App\Http\Requests;

use FFMpeg\FFMpeg;
use App\Jobs\ProcessVideo;
use App\Support\VideoDestinations;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
{
    /**
     * The key name of the video.
     *
     * @var string
     */
    protected $key;

    /**
     * Destination object.
     *
     * @var App\Support\VideoDestinations
     */
    protected $destinations;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct()
    {
        $this->key = md5(microtime());

        $this->destinations = new VideoDestinations($this->key);
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

        $destination = $this->destinations->prefix('app')->unprocessed . $file->getClientOriginalExtension();

        Storage::disk('root')
            ->put($destination, $source, 'private');

        ProcessVideo::dispatch(
            $this->destinations,
            storage_path($destination),
            $file->getClientOriginalName(),
            auth()->user()
        );

        return $this->destinations->getPaths();
    }
}
