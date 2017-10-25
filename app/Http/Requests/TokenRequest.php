<?php

namespace App\Http\Requests;

use Stripe\Charge;
use Stripe\Customer;
use App\Models\Purchase;
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
        return auth()->check()
            && auth()->user()->hasCardOnFile();
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

        $purchase = $package->purchase();

        auth()->user()->tokens += $package->token_count;
        auth()->user()->save();

        event(new TokensAdded(auth()->user(), $package->token_count));

        return [
            'style' => 'success',
            'amount' => $package->token_count,
            'total' => auth()->user()->tokens,
            'message' => "{$package->token_count} tokens added to your account",
        ];
    }
}
