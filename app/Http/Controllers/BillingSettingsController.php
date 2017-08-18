<?php

namespace App\Http\Controllers;

use Stripe\Customer;
use Illuminate\Http\Request;
use App\Exceptions\NoCustomerException;

class BillingSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($customer = auth()->user()->stripeCustomer()) {
            return response()->json($customer->sources->all([
                'object' => 'card',
                'limit' => 100
            ]));
        }

        throw new NoCustomerException;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get the stripe customer
        $customer = auth()->user()->stripeCustomer($request->stripeToken);

        // Add a new card to the customer
        $card = $customer->sources->create([
            'source' => $request->stripeToken
        ]);

        // Update our user reference
        auth()->user()->update([
            'stripe_id' => $customer->id,
            'card_brand' => $card->brand,
            'card_last_four' => $card->last4,
        ]);

        return $card;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($cardId)
    {
        if ($customer = auth()->user()->stripeCustomer()) {
            $customer->sources->retrieve($cardId)->delete();
            $this->cleanupCards();
        } else {
            throw new NoCustomerException;
        }
    }

    /**
     * Cleans up the card on file if no cards are available.
     *
     * @return void
     */
    public function cleanupCards()
    {
        if ($customer = auth()->user()->stripeCustomer()) {
            $cards = $customer->sources->all([
                'object' => 'card',
                'limit' => 100
            ])->data;

            if (empty($cards)) {
                auth()->user()->update([
                    'stripe_id' => null,
                    'card_brand' => null,
                    'card_last_four' => null,
                ]);
            }
        } else {
            throw new NoCustomerException;
        }
    }
}
