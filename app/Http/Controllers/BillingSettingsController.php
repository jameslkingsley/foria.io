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
        return [
            'card_brand' => auth()->user()->card_brand,
            'card_last_four' => auth()->user()->card_last_four,
            'has_card_on_file' => auth()->user()->has_card_on_file,
        ];
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
        try {
            // Create Stripe customer if none available
            if (! auth()->user()->stripe_id) {
                auth()->user()->createAsStripeCustomer($request->stripeToken);
            }

            // Update card details
            auth()->user()->updateCard($request->stripeToken);
        } catch (\Exception $e) {
            return abort(500, $e->getMessage());
        }
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
    public function destroy()
    {
        auth()->user()->cards()->each(function ($card) {
            $card->delete();
        });

        auth()->user()->update([
            'card_brand' => null,
            'card_last_four' => null,
        ]);
    }
}
