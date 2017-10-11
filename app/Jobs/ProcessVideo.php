<?php

namespace App\Jobs;

use FFMpeg\FFMpeg;
use App\Models\User;
use App\Models\Video;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\VideoTooShortException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\VideoProcessingFailed;
use App\Notifications\VideoProcessingComplete;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The video's paths.
     *
     * @var object
     */
    protected $paths;

    /**
     * The raw video file.
     *
     * @var string
     */
    protected $file;

    /**
     * Title of the video.
     *
     * @var string
     */
    protected $name;

    /**
     * The authenticated user.
     *
     * @var App\Models\User
     */
    protected $user;

    /**
     * The FFMpeg video instance.
     *
     * @var FFMpeg\Media\Video
     */
    protected $video;

    /**
     * The video model record.
     *
     * @var App\Models\Video
     */
    protected $record;

    /**
     * Interval in seconds to create thumbnails.
     *
     * @var integer
     */
    protected $thumbnailInterval = 6;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($paths, $file, $name, User $user)
    {
        $this->paths = $paths;
        $this->file = $file;
        $this->name = $name;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->createRecord();
            $this->convert();
            $this->checkDuration();
            $this->createThumbnails();
            $this->createPreview();
            $this->saveMeta();
            $this->deploy();
            $this->cacheThumbnails();

            $this->user->notify(
                new VideoProcessingComplete($this->record)
            );
        } catch (\Exception $e) {
            $this->user->notify(
                new VideoProcessingFailed($e->getMessage())
            );
        }
    }

    /**
     * Converts the video to MP4.
     *
     * @return void
     */
    protected function convert()
    {
        $video = FFMpeg::create()->open($this->file);

        $video->save(
            new X264('aac'),
            storage_path($this->paths->processed)
        );

        unlink($this->file);

        $this->video = FFMpeg::create()
            ->open(storage_path($this->paths->processed));
    }

    /**
     * Checks whether the duration is valid.
     *
     * @return void
     */
    public function checkDuration()
    {
        $duration = $this->video->getStreams()
            ->videos()
            ->first()
            ->get('duration');

        if ($duration < 60) {
            $this->record->delete();

            Storage::disk('root')
                ->deleteDirectory("app/{$this->paths->directory}");

            // TODO Raise event

            throw new VideoTooShortException;
        }
    }

    /**
     * Creates the thumbnails for the video.
     *
     * @return void
     */
    protected function createThumbnails()
    {
        $duration = $this->video->getStreams()
            ->videos()
            ->first()
            ->get('duration');

        // Create thumbnails directory
        mkdir(storage_path($this->paths->thumbnails));

        $frames = floor($duration / $this->thumbnailInterval);

        for ($i = 0; $i < $frames; $i++) {
            $seconds = $i * $frames;
            $frame = $this->video->frame(TimeCode::fromSeconds($seconds));
            $frame->save(storage_path("{$this->paths->thumbnails}/$seconds.jpg"));
        }
    }

    /**
     * Creates a short preview of the video.
     *
     * @return void
     */
    public function createPreview()
    {
        $video = FFMpeg::create()
            ->open(storage_path($this->paths->processed));

        $duration = $video->getStreams()
            ->videos()
            ->first()
            ->get('duration');

        $video
            ->filters()
            ->clip(
                TimeCode::fromSeconds(($duration / 2) - 10),
                TimeCode::fromSeconds(20)
            );

        $video->save(
            new X264('aac'),
            storage_path($this->paths->preview)
        );
    }

    /**
     * Persists this video to database storage.
     *
     * @return void
     */
    public function createRecord()
    {
        $this->record = Video::create([
            'user_id' => $this->user->id,
            'name' => str_before($this->name, '.'),
            'key' => $this->paths->key
        ]);
    }

    /**
     * Updates the video record with meta details.
     *
     * @return void
     */
    public function saveMeta()
    {
        $video = $this->video
            ->getStreams()
            ->videos()
            ->first();

        $this->record->update([
            'duration' => $video->get('duration'),
            'width' => $video->get('width'),
            'height' => $video->get('height'),
            'frame_rate' => (int) str_before($video->get('avg_frame_rate'), '/') / 1000,
        ]);
    }

    /**
     * Deploys the files to cloud storage.
     *
     * @return void
     */
    public function deploy()
    {
        //
    }

    /**
     * Stores the array of thumbnail URLs.
     *
     * @return void
     */
    public function cacheThumbnails()
    {
        //
    }
}
