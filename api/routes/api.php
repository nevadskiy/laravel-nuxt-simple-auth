<?php

Route::group([
    'prefix' => 'auth',
    'namespace' => 'Auth',
], function () {
    Route::post('signup', 'SignUpController@store')->name('api.auth.signup.store');
    Route::post('signin', 'SignInController@store')->name('api.auth.signin.store');
    Route::get('user', 'UserController@index')->name('api.auth.user.index');
    Route::post('forgot', 'ForgottenPasswordController@store')->name('api.auth.forgotten-password.store');
    Route::put('forgot', 'ForgottenPasswordController@update')->name('api.auth.forgotten-password.update');
});
