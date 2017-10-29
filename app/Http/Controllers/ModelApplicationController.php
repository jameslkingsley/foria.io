<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelApplicationController extends Controller
{
    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return vue('f-model-application');
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
        $attributes = $request->validate([
            'country' => 'required',
            'nicknames' => 'required',
            'full_name' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'maiden_name' => 'required_if:gender,female',
        ]);

        $attributes['user_id'] = auth()->user()->id;
        $attributes['date_of_birth'] = Carbon::parse($attributes['date_of_birth']);

        return Application::create($attributes);
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $attributes = $request->validate([
            'photo_id' => 'required|image',
            'photo_self' => 'required|image',
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::findOrFail($attributes['application_id']);

        $pathToId = Storage::cloud()->putFile(
            'models/'.auth()->user()->name.'/application',
            $request->file('photo_id')
        );

        $pathToSelf = Storage::cloud()->putFile(
            'models/'.auth()->user()->name.'/application',
            $request->file('photo_self')
        );

        $application->update([
            'photo_id' => $pathToId,
            'photo_self' => $pathToSelf,
        ]);
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
