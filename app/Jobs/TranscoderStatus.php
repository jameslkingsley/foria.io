<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use App\Events\TranscodeCompleted;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Aws\ElasticTranscoder\ElasticTranscoderClient;

class TranscoderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The video that is being encoded.
     *
     * @var App\Models\Video
     */
    protected $video;

    /**
     * User instance.
     *
     * @var App\Models\User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Video $video)
    {
        $this->video = $video;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ElasticTranscoderClient $transcoder)
    {
        // Get the transcoder status from AWS
        $job = $transcoder->readJob([
            'Id' => $this->video->transcoder_id
        ])->toArray()['Job'];

        if ($job['Status'] == 'Complete') {
            // If the transcoder has completed, update the video
            $this->video->update([
                'has_processed' => true,
                'duration' => $job['Output']['Duration'],
                'width' => $job['Output']['Width'],
                'height' => $job['Output']['Height'],
                'frame_rate' => $job['Output']['FrameRate'],
            ]);

            // Raise the event for the client feedback
            event(new TranscodeCompleted($this->user, $this->video));
        } elseif ($job['Status'] == 'Submitted' || $job['Status'] == 'Progressing') {
            // If transcoder is still processing, run this job again in 5 seconds
            static::dispatch($this->video)
                ->delay(Carbon::now()->addSeconds(5));
        }
    }
}
