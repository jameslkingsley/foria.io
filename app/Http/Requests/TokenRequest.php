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
            'stripeToken' => 'required',
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
        $customer = $this->customer();

        $package = TokenPackage::findOrFail($this->package_id);

        try {
            $charge = Charge::create([
                'amount' => $package->cost,
                'currency' => 'gbp',
                'customer' => $customer->id
            ]);
        } catch (\Exception $e) {
            return $e;
        }

        auth()->user()->tokens += $package->token_count;
        auth()->user()->save();

        event(new TokensAdded($package->token_count));

        return [
            'message' => "{$package->token_count} tokens added to your account",
            'style' => 'success'
        ];
    }

    /**
     * Gets the Stripe customer instance.
     *
     * @return Stripe\Customer
     */
    public function customer()
    {
        if (! $customer = auth()->user()->stripeCustomer()) {
            $customer = Customer::create([
                'email' => auth()->user()->email,
                'source' => $this->stripeToken
            ]);

            auth()->user()->stripe_customer_id = $customer->id;
            auth()->user()->save();
        }

        return $customer;
    }
}
