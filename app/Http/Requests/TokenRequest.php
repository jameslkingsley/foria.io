<?php

namespace App\Http\Requests;

use Stripe\Charge;
use Stripe\Customer;
use App\Events\TokensAdded;
use App\Models\TokenPackage;
use Illuminate\Foundation\Http\FormRequest;

class TokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return ! auth()->guest() && auth()->user()->hasCardOnFile();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'package_id' => 'required|integer'
        ];
    }

    /**
     * Handles the request.
     *
     * @return \Illuminate\Http\Response
     */
    public function handle()
    {
        $package = TokenPackage::findOrFail($this->package_id);

        auth()->user()->charge($package->cost);

        auth()->user()->tokens += $package->token_count;
        auth()->user()->save();

        event(new TokensAdded(auth()->user(), $package->token_count));

        return [
            'message' => "{$package->token_count} tokens added to your account",
            'style' => 'success',
            'amount' => $package->token_count,
            'total' => auth()->user()->tokens
        ];
    }
}
