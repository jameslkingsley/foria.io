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

        if (auth()->user()->hasCardOnFile()) {
            // Use card on file
            try {
                auth()->user()->charge($package->cost);
            } catch (\Exception $e) {
                return $e;
            }
        } else {
            // Use given card token
            $customer = auth()->user()->stripeCustomer($this->stripeToken);

            try {
                $charge = Charge::create([
                    'amount' => $package->cost,
                    'currency' => 'gbp',
                    'customer' => $customer->id
                ]);
            } catch (\Exception $e) {
                return $e;
            }
        }

        auth()->user()->tokens += $package->token_count;
        auth()->user()->save();

        event(new TokensAdded($package->token_count));

        return [
            'message' => "{$package->token_count} tokens added to your account",
            'style' => 'success'
        ];
    }
}
