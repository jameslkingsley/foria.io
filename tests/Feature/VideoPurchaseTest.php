<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoPurchaseTest extends TestCase
{
    /** @test */
    public function cannotPurchasePrivateVideo()
    {
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $video = factory(Video::class)->create([
            'privacy' => 'private',
            'token_price' => 500
        ]);

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post("/api/purchase/{$video->ref}", [
                '_token' => $this->token
            ]);

        $response->assertStatus(403);

        $this->assertFalse($video->purchased());
    }

    /** @test */
    public function cannotPurchaseVideoWhenUnauthenticated()
    {
        $this->assertGuest();

        $video = factory(Video::class)->create([
            'privacy' => 'public',
            'token_price' => 500
        ]);

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post("/api/purchase/{$video->ref}", [
                '_token' => $this->token
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function canPurchaseVideoWithTokens()
    {
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $video = factory(Video::class)->create([
            'privacy' => 'public',
            'token_price' => 500
        ]);

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post("/api/purchase/{$video->ref}", [
                '_token' => $this->token
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => $video->purchases()->first()->toArray(),
            ]);

        $this->assertTrue($video->purchased());
    }
}
