<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Video;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RatingTest extends TestCase
{
    /**
     * Sets up the test case.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->video = factory(Video::class)->create();
        $this->user = factory(User::class)->create();

        auth()->login($this->user);
    }

    /** @test */
    public function canRateVideo()
    {
        $rating = $this->video->rate('like');

        $this->assertInstanceOf(Rating::class, $rating);
    }

    /** @test */
    public function canUnrateVideo()
    {
        $this->video->rate('like');
        $this->video->unrate();

        $this->assertFalse($this->video->hasLiked());
    }

    /** @test */
    public function canGetRatings()
    {
        $this->assertInstanceOf(MorphMany::class, $this->video->ratings());
    }

    /** @test */
    public function canGetRatingsCompact()
    {
        $this->assertTrue(is_array($this->video->ratingsCompact()));
    }

    /** @test */
    public function canGetSingleRating()
    {
        $this->video->rate('like');

        $this->assertInstanceOf(Rating::class, $this->video->rating());
    }
}
