<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Rules\IsModel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class RatingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Model $model)
    {
        $attributes = (object) $request->validate([
            'type' => 'required|string|in:like,dislike'
        ]);

        $model->unrate();

        $model->rate($attributes->type);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function show(Model $model)
    {
        $ratings = $model->ratingsCompact();

        return response()->json($ratings);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $model)
    {
        $model->unrate();
    }
}
