<?php

Route::get('/', 'AppController@index')->name('app.index');

// Auth routes...
Route::get('auth/login',        'Auth\AuthController@getLogin')->name('auth.login');
Route::post('auth/login',       'Auth\AuthController@postLogin');
Route::get('auth/logout',       'Auth\AuthController@getLogout')->name('auth.logout');
Route::get('auth/register',     'Auth\AuthController@getRegister')->name('auth.register');
Route::post('auth/register',    'Auth\AuthController@postRegister');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail')->name('password.email');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

// Posts routes...
Route::post('posts/{posts}/photos', 'PostsController@postPhotos')->name('posts.photos');
Route::resource('posts', 'PostsController', ['only' => ['index', 'store', 'show', 'destroy']]);

// Profile routes...
Route::post('profiles/{profiles}/photos', 'ProfilesController@postPhotos')->name('profiles.photos');
Route::delete('profiles/{profiles}/photos', 'ProfilesController@deletePhotos');
Route::post('profiles/{profiles}/approve', 'ProfilesController@postApprove')->name('profiles.approve');
Route::post('profiles/{profiles}/unapprove', 'ProfilesController@postUnapprove')->name('profiles.unapprove');
Route::post('profiles/{profiles}/feature', 'ProfilesController@postFeature')->name('profiles.feature');
Route::post('profiles/{profiles}/unfeature', 'ProfilesController@postUnfeature')->name('profiles.unfeature');
Route::resource('profiles', 'ProfilesController');

// Blog routes
Route::resource('blogs', 'BlogsController');

Route::group(['prefix' => 'api'], function() {
    Route::resource('authenticate', 'AuthenticateController');
    Route::post('authenticate', 'AuthenticateController@authenticate');
});

// API Routes
Route::controller('api', 'APIController', [
    'getPosts' => 'api.posts',
    'getPost' => 'api.post',
    'getProfiles' => 'api.profiles',
    'getProfile' => 'api.profile',

    'getProfilesv1' => 'api.profilesv1',
    'getpostsv1' => 'api.postsv1',
    'getfavs' => 'api.favs',
    'getsearch' => 'api.search',
    'getEvents' => 'api.events',
    'getBlogs' => 'api.blogs',
    'getBookmarks' => 'api.bookmarks'
]);