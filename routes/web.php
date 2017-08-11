<?php

// Homepage
Route::get('/', 'HomeController@index')->name('home');

// Watching
Route::get('/watch/{name}', 'WatchController@index');
Route::get('/watch/{user}/show', 'WatchController@show');
Route::post('/watch/{user}/start', 'WatchController@start');

// Authentication
Route::get('/logout', 'Auth\LogoutController@index');
Auth::routes();

// Following
Route::get('/follow/{to}', 'FollowController@index');
Route::post('/follow/{to}', 'FollowController@store');
Route::delete('/follow/{to}', 'FollowController@destroy');
