<?php

Route::get('/', 'HomeController@index')->name('home');

Route::get('/logout', 'Auth\LogoutController@index');
Auth::routes();
