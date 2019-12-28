<?php

use Module\Auth\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::group([
        'middleware' => 'auth',
    ], function () {
        Route::delete('signout', [Api\SignOutController::class, 'destroy'])->name('api.auth.signout.destroy');
        Route::get('user', [Api\UserController::class, 'index'])->name('api.auth.user.index');
    });

    Route::group([
        'middleware' => 'guest',
    ], function () {
        Route::post('signup', [Api\SignUpController::class, 'store'])->name('api.auth.signup.store');
        Route::post('signin', [Api\SignInController::class, 'store'])->name('api.auth.signin.store');

        Route::post('password/forgot', [Api\PasswordController::class, 'forgot'])->name('api.auth.password.forgot');
        Route::put('password/reset', [Api\PasswordController::class, 'reset'])->name('api.auth.password.reset');
    });
});
