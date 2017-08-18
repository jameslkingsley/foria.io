<?php

namespace App\Http\Controllers;

use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Error\Api;
use Stripe\Error\Card;
use App\Models\TokenPackage;
use Illuminate\Http\Request;
use Stripe\Error\ApiConnection;
use Stripe\Error\InvalidRequest;
use App\Http\Requests\TokenRequest;

class TokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tokens.index');
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
    public function store(TokenRequest $request)
    {
        $response = null;

        try {
            $response = $request->handle();
        } catch (\Exception $e) {
            $response = [
                'message' => $e->getMessage(),
                'style' => 'danger'
            ];
        }

        return $response;
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
    public function destroy($id)
    {
        //
    }

    /**
     * Gets the token packages as JSON.
     *
     * @return mixed
     */
    public function packages()
    {
        return response()->json(TokenPackage::all());
    }
}
