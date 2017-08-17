<?php

namespace App\Models;

use App\Models\User;
use App\Models\Broadcast;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'sender'
    ];

    /**
     * Gets the sender user model.
     *
     * @return App\Models\User
     */
    public function getSenderAttribute()
    {
        return User::where('id', $this->sender_id)->first();
    }

    /**
     * Gets the receiver user model.
     *
     * @return App\Models\User
     */
    public function receiver()
    {
        return User::where('id', $this->receiver_id)->first();
    }

    /**
     * Gets the broadcast model.
     *
     * @return App\Models\Broadcast
     */
    public function broadcast()
    {
        return Broadcast::where('id', $this->broadcast_id)->first();
    }
}
