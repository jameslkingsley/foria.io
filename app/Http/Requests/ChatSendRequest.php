<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Broadcast;
use Illuminate\Foundation\Http\FormRequest;

class ChatSendRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO Blocked users

        return ! auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => 'required|string|min:1'
        ];
    }

    /**
     * Gets the chat model attributes.
     *
     * @return array
     */
    public function attributes()
    {
        $receiver = User::findOrFail($this->receiver['id']);
        $broadcast = is_null($this->broadcast) ? null : Broadcast::findOrFail($this->broadcast['id']);

        return [
            'text' => $this->text,
            'sender_id' => auth()->user()->id,
            'receiver_id' => $receiver->id,
            'broadcast_id' => $broadcast ? $broadcast->id : null,
        ];
    }
}
