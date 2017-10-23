<?php

// Homepage
Route::get('/', 'HomeController@index')->name('home');

// Reporting
Route::post('/api/report/{ref}', 'ReportController@store');
Route::get('/api/report/{ref}', 'ReportController@index');

// Profile
Route::get('/profile/{user}', 'ProfileController@index');

// Notifications
Route::delete('/api/notifications/clear', 'NotificationController@destroy');

// Watching
Route::get('/watch/{user}', 'WatchController@index');
Route::get('/api/watch/{user}', 'WatchController@show');

// Broadcasts
Route::resource('/api/broadcast-query', 'BroadcastQueryController');
Route::post('/api/broadcast', 'BroadcastController@store');
Route::delete('/api/broadcast', 'BroadcastController@destroy');

// Topic
Route::patch('/api/topic', 'TopicController@update');

// Videos
Route::get('/api/videos/list/{user}', 'VideoController@index');
Route::get('/api/videos/{ref}', 'VideoController@showJson');
Route::post('/api/videos', 'VideoController@store');
Route::post('/api/videos/{ref}', 'VideoController@update');
Route::get('/api/videos/{ref}/edit', 'VideoController@edit');
Route::get('/videos/upload', 'VideoController@create');
Route::get('/videos/manager', 'VideoManagerController@index');
Route::get('/videos/{ref}', 'VideoController@show');

// Comments
Route::get('/api/comments/{ref}', 'CommentController@index');
Route::post('/api/comments/{ref}', 'CommentController@store');

// Ratings
Route::get('/api/ratings/{ref}', 'RatingController@show');

Route::middleware(['auth'])->group(function () {
    Route::post('/api/ratings/{ref}', 'RatingController@store');
    Route::delete('/api/ratings/{ref}', 'RatingController@destroy');
});

// Purchases
Route::get('/api/purchase/{ref}', 'PurchaseController@show');
Route::post('/api/purchase/{ref}', 'PurchaseController@store');

// Chat
Route::get('/api/chat/past/{user}', 'ChatController@show');
Route::resource('/api/chat', 'ChatController');

// Authentication
Route::get('/logout', 'Auth\LogoutController@index');
Auth::routes();

// Tokens
Route::get('/tokens/packages', 'TokenController@packages');
Route::resource('/tokens', 'TokenController');

// Following
Route::get('/api/follow/{user}', 'FollowController@index');
Route::post('/api/follow/{user}', 'FollowController@store');
Route::delete('/api/follow/{user}', 'FollowController@destroy');

// Subscriptions
Route::get('/api/subscription/{user}', 'SubscriptionController@show');
Route::delete('/api/subscription/{user}', 'SubscriptionController@destroy');
Route::resource('/api/subscription', 'SubscriptionController');

// Settings
Route::resource('/settings', 'SettingsController', [
    'except' => ['show']
]);

Route::post('/api/settings/avatar', 'AvatarSettingsController@store');
Route::resource('/settings/account', 'AccountSettingsController');
Route::resource('/settings/billing', 'BillingSettingsController');
Route::resource('/settings/model', 'ModelSettingsController');

Route::get('/stream', function () {
    return vue('f-stream-test');
});
