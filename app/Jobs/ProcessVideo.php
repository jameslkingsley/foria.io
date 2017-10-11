<?php

namespace App\Jobs;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
     * The FFMpeg video instance.
     *
     * @var FFMpeg\Media\Video
     */
    protected $video;

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
    public function __construct($paths, $file)
    {
        $this->paths = $paths;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->convert();
        $this->createThumbnails();
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
}
