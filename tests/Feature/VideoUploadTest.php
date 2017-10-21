<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\ProcessVideo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoUploadTest extends TestCase
{
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

        Storage::disk('root')->assertExists("{$data->unprocessed}mp4");

        Queue::assertPushed(ProcessVideo::class, function ($job) use ($data) {
            return $job->paths->key === $data->key;
        });

        (new ProcessVideo(
            $data,
            storage_path("{$data->unprocessed}mp4"),
            'Test Video - Too Short',
            $this->user,
            $this // Pass the testing object
        ))->handle();

        // Storage::disk('root')->deleteDirectory($data->directory);
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

        Storage::disk('root')->assertExists("{$data->unprocessed}mkv");

        Queue::assertPushed(ProcessVideo::class, function ($job) use ($data) {
            return $job->paths->key === $data->key;
        });

        (new ProcessVideo(
            $data,
            storage_path("{$data->unprocessed}mkv"),
            'Test Video - One Minute',
            $this->user,
            $this // Pass the testing object
        ))->handle();
    }
}
