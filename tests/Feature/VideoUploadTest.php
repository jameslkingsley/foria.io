<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\ProcessVideo;
use Illuminate\Http\UploadedFile;
use App\Support\VideoDestinations;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoUploadTest extends TestCase
{
    /**
     * Whether the longer 5 minute video test should run.
     *
     * @var boolean
     */
    protected $runLongTests = false;

    /** @test */
    public function shortVideoGetsRejected()
    {
        Queue::fake();

        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $token = str_random(20);
        $video = new UploadedFile(
            base_path('tests/Assets/too_short.mp4'),
            'too_short.mp4',
            'video/mp4'
        );

        $response = $this
            ->withSession(['_token' => $token])
            ->post('/api/videos', [
                '_token' => $token,
                'video' => $video
            ]);

        $response->assertStatus(200);

        $data = json_decode($response->getContent());

        Storage::disk('root')->assertExists("app/{$data->unprocessed}mp4");

        Queue::assertPushed(ProcessVideo::class, function ($job) use ($data) {
            return $job->paths->key === $data->key;
        });

        (new ProcessVideo(
            VideoDestinations::make($data),
            storage_path("app/{$data->unprocessed}mp4"),
            'Test Video - Too Short',
            $this->user,
            $this // Pass the testing object
        ))->handle();

        Storage::disk('root')->deleteDirectory($data->directory);
    }

    /** @test */
    public function oneMinuteVideoGetsAccepted()
    {
        Queue::fake();

        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $token = str_random(20);
        $video = new UploadedFile(
            base_path('tests/Assets/one_minute.mkv'),
            'one_minute.mkv',
            'video/*'
        );

        $response = $this
            ->withSession(['_token' => $token])
            ->post('/api/videos', [
                '_token' => $token,
                'video' => $video
            ]);

        $response->assertStatus(200);

        $data = json_decode($response->getContent());

        Storage::disk('root')->assertExists("app/{$data->unprocessed}mkv");

        Queue::assertPushed(ProcessVideo::class, function ($job) use ($data) {
            return $job->paths->key === $data->key;
        });

        (new ProcessVideo(
            VideoDestinations::make($data),
            storage_path("app/{$data->unprocessed}mkv"),
            'Test Video - One Minute',
            $this->user,
            $this // Pass the testing object
        ))->handle();

        Storage::disk('root')->deleteDirectory($data->directory);
    }

    /** @test */
    public function fiveMinuteVideoGetsAccepted()
    {
        if (! $this->runLongTests) {
            $this->assertTrue(true);
            return;
        }

        Queue::fake();

        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $token = str_random(20);
        $video = new UploadedFile(
            base_path('tests/Assets/five_minutes.mkv'),
            'five_minutes.mkv',
            'video/*'
        );

        $response = $this
            ->withSession(['_token' => $token])
            ->post('/api/videos', [
                '_token' => $token,
                'video' => $video
            ]);

        $response->assertStatus(200);

        $data = json_decode($response->getContent());

        Storage::disk('root')->assertExists("app/{$data->unprocessed}mkv");

        Queue::assertPushed(ProcessVideo::class, function ($job) use ($data) {
            return $job->paths->key === $data->key;
        });

        (new ProcessVideo(
            VideoDestinations::make($data),
            storage_path("app/{$data->unprocessed}mkv"),
            'Test Video - Five Minutes',
            $this->user,
            $this // Pass the testing object
        ))->handle();

        Storage::disk('root')->deleteDirectory($data->directory);
    }
}
