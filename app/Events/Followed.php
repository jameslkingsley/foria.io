<?php

namespace App\Events;

use App\Models\Follow;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Followed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Follow model.
     *
     * @var App\Models\Follow
     */
    public $follow;

    /**
     * The new follower count.
     *
     * @var integer
     */
    public $count;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Follow $follow)
    {
        $this->follow = $follow;
        $this->count = $follow->model->follower_count;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("followed-{$this->follow->to_id}");
    }
}
