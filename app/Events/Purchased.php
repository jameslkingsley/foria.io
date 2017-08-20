<?php

namespace App\Events;

use App\Models\Purchase;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Purchased implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Purchase record.
     *
     * @var App\Models\Purchase
     */
    public $purchase;

    /**
     * All purchase records for the user.
     *
     * @var Collection App\Models\Purchase
     */
    public $purchases;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
        $this->purchases = $purchase->user->purchases();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("App.User.{$this->purchase->user_id}");
    }
}
