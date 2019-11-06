<?php

Route::group([
    'namespace' => 'Auth'
], function () {
    Route::post('signup', 'SignUpController@store')->name('api.auth.signup.store');
    Route::post('signin', 'SignInController@store')->name('api.auth.signin.store');
    Route::get('user', 'UserController@index')->name('api.auth.user.index');
});
