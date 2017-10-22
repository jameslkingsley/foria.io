<?php

namespace App\Jobs;

use FFMpeg\FFMpeg;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\File;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\VideoTooShortException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\VideoProcessingFailed;
use App\Notifications\VideoProcessingComplete;
use PHPUnit\Framework\ExpectationFailedException;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Destination object.
     *
     * @var App\Support\VideoDestinations
     */
    public $paths;

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
     * The tester object.
     *
     * @var object|null
     */
    protected $tester;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($paths, $file, $name, User $user, $tester = null)
    {
        $this->paths = $paths;
        $this->file = $file;
        $this->name = $name;
        $this->user = $user;
        $this->tester = $tester;
    }

    /**
     * Gets the tester object or a null proxy.
     *
     * @return object
     */
    public function tester()
    {
        return optional($this->tester);
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

            $this->record->update([
                'has_processed' => true
            ]);

            $this->user->notify(
                new VideoProcessingComplete($this->record)
            );
        } catch (ExpectationFailedException $e) {
            $this->tester()->assertTrue(false, $e->getMessage());
        } catch (\Exception $e) {
            \Log::error($e->getMessage());

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
            storage_path($this->paths->prefix('app')->processed)
        );

        $this->tester()->assertTrue(
            file_exists(storage_path($this->paths->prefix('app')->processed)),
            'Processed video does not exist'
        );

        unlink($this->file);

        $this->tester()->assertFalse(
            file_exists($this->file),
            'Original client file was not deleted'
        );

        $this->video = FFMpeg::create()
            ->open(storage_path($this->paths->prefix('app')->processed));
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

            $this->tester()->assertNull(
                $this->record->fresh(),
                'Record still exists after failing duration check'
            );

            Storage::disk('root')
                ->deleteDirectory($this->paths->prefix('app')->directory);

            $this->tester()->assertFalse(
                file_exists(storage_path($this->paths->prefix('app')->directory)),
                'Directory still exists after failing duration check'
            );

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
        mkdir(storage_path($this->paths->prefix('app')->thumbnails));

        $this->tester()->assertTrue(
            file_exists(storage_path($this->paths->prefix('app')->thumbnails)),
            'Thumbnail directory was not created'
        );

        $frames = floor($duration / $this->thumbnailInterval);

        foreach (range(0, $frames) as $index) {
            $seconds = $index * $frames;
            $frame = $this->video->frame(TimeCode::fromSeconds($seconds));
            $path = storage_path("{$this->paths->prefix('app')->thumbnails}/$seconds.jpg");
            $frame->save($path);
        }
    }

    /**
     * Creates a short preview of the video.
     *
     * @return void
     */
    public function createPreview()
    {
        /**
         * TODO
         * Clips different sections of the video
         * Instead of just the center 20 seconds
         */

        $video = FFMpeg::create()
            ->open(storage_path($this->paths->prefix('app')->processed));

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
            storage_path($this->paths->prefix('app')->preview)
        );

        $this->tester()->assertTrue(
            file_exists(storage_path($this->paths->prefix('app')->preview)),
            'Preview video does not exist'
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

        $this->tester()->assertTrue(
            $this->record->exists(),
            'Record was not created'
        );
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
        $local = Storage::disk('root');
        $cloud = Storage::cloud();

        $cloud->makeDirectory($this->paths->directory);
        $cloud->makeDirectory($this->paths->thumbnails);

        $cloud->assertExists($this->paths->directory);
        $cloud->assertExists($this->paths->thumbnails);

        $cloud->putFileAs(
            $this->paths->directory,
            new File(storage_path($this->paths->prefix('app')->processed)),
            'processed.mp4',
            'private'
        );

        $cloud->assertExists($this->paths->processed);

        $cloud->putFileAs(
            $this->paths->directory,
            new File(storage_path($this->paths->prefix('app')->preview)),
            'preview.mp4',
            'public'
        );

        $cloud->assertExists($this->paths->preview);

        $thumbnails = $local->files($this->paths->prefix('app')->thumbnails);

        foreach ($thumbnails as $thumbnail) {
            $cloud->putFileAs(
                $this->paths->thumbnails,
                new File(storage_path($thumbnail)),
                basename($thumbnail),
                'public'
            );

            $cloud->assertExists($this->paths->thumbnails.'/'.basename($thumbnail));
        }

        $local->deleteDirectory($this->paths->prefix('app')->directory);
    }

    /**
     * Stores the array of thumbnail URLs.
     *
     * @return void
     */
    public function cacheThumbnails()
    {
        $urls = [];
        $thumbnails = Storage::cloud()->files($this->paths->thumbnails);

        foreach ($thumbnails as $thumbnail) {
            $urls[] = Storage::cloud()->url($thumbnail);
        }

        $this->record->update([
            'thumbnails' => json_encode($urls)
        ]);

        $this->tester()->assertNotNull(
            $this->record->fresh()->thumbnails,
            'Thumbnails cache is still null'
        );
    }
}
