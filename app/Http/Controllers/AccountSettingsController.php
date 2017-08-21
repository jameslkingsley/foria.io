<?php

namespace App\Http\Controllers;

use App\Rules\Username;
use Illuminate\Http\Request;
use App\Rules\CurrentPassword;

class AccountSettingsController extends Controller
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
        return [
            'user' => auth()->user()
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
        switch ($request->method) {
            case 'username':
                $attributes = $request->validate([
                    'name' => ['required', 'string', new Username]
                ]);

                auth()->user()->update($attributes);

                break;

            case 'email':
                $attributes = $request->validate([
                    'email' => 'required|string|confirmed|unique:users,email'
                ]);

                auth()->user()->update($attributes);

                break;

            case 'password':
                $attributes = $request->validate([
                    'current_password' => ['required', 'string', new CurrentPassword],
                    'password' => 'required|string|confirmed|min:6'
                ]);

                auth()->user()->password = bcrypt($attributes['password']);
                auth()->user()->save();

                break;
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
    public function destroy($id)
    {
        //
    }
}
