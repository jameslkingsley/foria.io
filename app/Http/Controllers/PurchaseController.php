<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\NoCustomerException;
use Illuminate\Auth\AuthenticationException;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request, string $type, int $id)
    {
        try {
            $model = 'App\\Models\\'.studly_case($type);

            if (! class_exists($model)) {
                throw new InvalidArgumentException;
            }

            $model = $model::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $model->purchase()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $type, int $id)
    {
        $model = 'App\\Models\\'.studly_case($type);

        if (! class_exists($model)) {
            throw new InvalidArgumentException;
        }

        $model = $model::findOrFail($id);

        return response()->json([
            'purchase' => $model->purchases->first()
        ]);
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
}
