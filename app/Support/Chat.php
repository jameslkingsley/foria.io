<?php

namespace App\Support;

use App\Models\User;
use App\Models\Broadcast;
use App\Events\ChatMessageSent;
use App\Models\Chat as ChatModel;

class Chat
{
    /**
     * The user the chat stream belongs to.
     *
     * @var App\Models\User
     */
    protected $user;

    /**
     * The Chat model resource.
     *
     * @var App\Models\Chat
     */
    protected $repository;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'is_subscription' => false,
        'is_token' => false,
    ];

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->repository = new ChatModel;
    }

    /**
     * Gets the current broadcast for the model user.
     *
     * @return App\Models\Broadcast|null
     */
    public function broadcast()
    {
        $broadcast = request('broadcast');

        return is_null($broadcast) ? null : Broadcast::findOrFail($broadcast['id']);
    }

    /**
     * Gets the broadcast ID or null.
     *
     * @return integer|null
     */
    public function broadcastId()
    {
        $broadcast = $this->broadcast();

        return is_null($broadcast) ? null : $broadcast->id;
    }

    /**
     * Makes a message.
     *
     * @return mixed
     */
    public function message(string $message, array $options = [])
    {
        $options = array_merge($this->options, $options);

        $chat = $this->repository->create([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $this->user->id,
            'broadcast_id' => $this->broadcastId(),
            'text' => $message,
            'options' => $options,
        ]);

        event(new ChatMessageSent($chat));
    }

    /**
     * Makes an alert with the given options.
     *
     * @return App\Support\Chat
     */
    public function alert(string $message, array $options = [])
    {
        $options = array_merge($this->options, $options);

        $chat = $this->repository->create([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $this->user->id,
            'broadcast_id' => $this->broadcastId(),
            'text' => $message,
            'options' => $options,
            'is_alert' => true,
        ]);

        event(new ChatMessageSent($chat));
    }
}
