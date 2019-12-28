<?php

use Module\Auth\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {

    Route::group([
        'middleware' => 'guest',
    ], function () {

        Route::post('signup', Api\SignUpController::class)->name('api.auth.sign-up');
        Route::post('signin', Api\SignInController::class)->name('api.auth.sign-in');

        Route::post('password/forgot', [Api\PasswordController::class, 'forgot'])->name('api.auth.password.forgot');
        Route::put('password/reset', [Api\PasswordController::class, 'reset'])->name('api.auth.password.reset');

    });

    Route::group([
        'middleware' => 'auth',
    ], function () {

        Route::delete('signout', Api\SignOutController::class)->name('api.auth.sign-out');

        Route::get('user', Api\UserController::class)->name('api.auth.user');

    });

});
