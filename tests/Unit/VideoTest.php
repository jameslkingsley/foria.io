<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Video;

class VideoTest extends TestCase
{
    /** @test */
    public function canPurchaseVideo()
    {
        $video = factory(Video::class)->create();

        auth()->login($video->user);

        $purchase = $video->purchase();

        $this->assertTrue($purchase->exists);
    }
}
