<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ReferenceTest extends TestCase
{
    /** @test */
    public function referenceIsAdded()
    {
        $video = factory(Video::class)->create();

        $this->assertNotNull($video->ref);
    }

    /** @test */
    public function canGetReferences()
    {
        $video = factory(Video::class)->create();

        $this->assertInstanceOf(MorphMany::class, $video->references());
    }
}
