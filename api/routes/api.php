<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'namespace' => 'Auth'
], function () {
    Route::post('signup', 'SignUpController@store')->name('api.auth.signup.store');
    Route::post('signin', 'SignInController@store')->name('api.auth.signin.store');
});
