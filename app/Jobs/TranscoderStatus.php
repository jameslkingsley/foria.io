<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use App\Events\TranscodeCompleted;
use Illuminate\Queue\SerializesModels;
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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ElasticTranscoderClient $transcoder)
    {
        // Get the transcoder status from AWS
        $status = $transcoder->readJob([
            'Id' => $this->video->transcoder_id
        ])->toArray()['Job']['Status'];

        if ($status == 'Complete') {
            // If the transcoder has completed, update the video
            $this->video->update(['has_processed' => true]);

            // Raise the event for the client feedback
            event(new TranscodeCompleted($this->video));
        } elseif ($status == 'Submitted' || $status == 'Progressing') {
            // If transcoder is still processing, run this job again in 5 seconds
            static::dispatch($this->video)
                ->delay(Carbon::now()->addSeconds(5));
        }
    }
}
