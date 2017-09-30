<?php

namespace App\Http\Requests;

use App\Models\Broadcast;
use Illuminate\Foundation\Http\FormRequest;

class BroadcastRequest extends FormRequest
{
    /**
     * Broadcast model instance.
     *
     * @var App\Models\Broadcast
     */
    protected $broadcast;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->broadcast = auth()->check() ? Broadcast::latest() : null;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check()
            && auth()->user()->is_model
            && ! is_null($this->broadcast);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Gets the broadcast.
     *
     * @return App\Models\Broadcast
     */
    public function broadcast()
    {
        return $this->broadcast;
    }
}
