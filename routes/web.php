<?php

Route::get('/', 'HomeController@index')->name('home');

Route::get('/watch/{name}', 'WatchController@index');
Route::get('/watch/{user}/show', 'WatchController@show');
Route::post('/watch/{user}/start', 'WatchController@start');

Route::get('/logout', 'Auth\LogoutController@index');
Auth::routes();
