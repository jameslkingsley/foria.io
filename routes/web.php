<?php

// Homepage
Route::get('/', 'HomeController@index')->name('home');

// Watching
Route::get('/watch/{name}', 'WatchController@index');
Route::get('/watch/{user}/show', 'WatchController@show');
Route::post('/watch/{user}/start', 'WatchController@start');

// Broadcasts
Route::resource('/api/broadcast-query', 'BroadcastQueryController');

Route::post('/api/broadcast/topic', 'BroadcastController@topic');
Route::post('/api/broadcast/start', 'BroadcastController@start');
Route::delete('/api/broadcast/stop', 'BroadcastController@stop');
Route::resource('/api/broadcast', 'BroadcastController');

// Authentication
Route::get('/logout', 'Auth\LogoutController@index');
Auth::routes();

// Tokens
Route::get('/tokens/packages', 'TokenController@packages');
Route::resource('/tokens', 'TokenController');

// Following
Route::get('/follow/{to}', 'FollowController@index');
Route::post('/follow/{to}', 'FollowController@store');
Route::delete('/follow/{to}', 'FollowController@destroy');
