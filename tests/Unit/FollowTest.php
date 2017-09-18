<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FollowTest extends TestCase
{
    /** @test */
    public function canFollowUser()
    {
        $from = factory(User::class)->create();
        $to = factory(User::class)->create();

        $follow = $from->follow($to);

        $this->assertInstanceOf(Follow::class, $follow);
    }

    /** @test */
    public function canUnfollowUser()
    {
        $from = factory(User::class)->create();
        $to = factory(User::class)->create();

        $from->follow($to);
        $from->unfollow($to);

        $this->assertFalse($from->isFollowing($to));
    }

    /** @test */
    public function canGetFollowers()
    {
        $target = factory(User::class)->create();
        $users = factory(User::class, 5)->create();

        foreach ($users as $user) {
            $user->follow($target);
        }

        $this->assertInstanceOf(MorphMany::class, $target->followers());
    }

    /** @test */
    public function canGetFollowerCount()
    {
        $target = factory(User::class)->create();
        $users = factory(User::class, 5)->create();

        foreach ($users as $user) {
            $user->follow($target);
        }

        $this->assertEquals(5, $target->follower_count);
    }
}
