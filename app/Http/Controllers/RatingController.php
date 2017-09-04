<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Rules\IsModel;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = (object) $request->validate([
            'model_id' => 'required|numeric',
            'type' => 'required|string|in:like,dislike',
            'model_type' => ['required', 'string', new IsModel],
        ]);

        $model = Rating::resolveModel($attributes->model_type, $attributes->model_id);

        $model->unrate();

        $model->rate($attributes->type);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function show(string $type, int $id)
    {
        $model = Rating::resolveModel($type, $id);

        $ratings = $model->ratingsCompact();

        return response()->json($ratings);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $type, int $id)
    {
        $model = Rating::resolveModel($type, $id);

        $model->unrate();
    }
}
