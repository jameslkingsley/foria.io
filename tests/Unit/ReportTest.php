<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Video;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ReportTest extends TestCase
{
    /** @test */
    public function canReportVideo()
    {
        $video = factory(Video::class)->create();

        $report = $video->report('COPYRIGHT', 'Some example body');

        $this->assertInstanceOf(Report::class, $report);
    }

    /** @test */
    public function canGetReports()
    {
        $video = factory(Video::class)->create();

        $this->assertInstanceOf(MorphMany::class, $video->reports());
    }
}
