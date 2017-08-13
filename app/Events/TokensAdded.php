<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TokensAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Number of tokens that were added.
     *
     * @var integer
     */
    public $tokens;

    /**
     * News total token count.
     *
     * @var integer
     */
    public $total;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $tokens)
    {
        $this->tokens = $tokens;
        $this->total = auth()->user()->tokens;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.'.auth()->user()->id);
    }
}
