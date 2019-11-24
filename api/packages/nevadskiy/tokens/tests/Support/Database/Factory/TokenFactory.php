<?php

use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;
use Nevadskiy\Tokens\Tests\Support\Models\User;
use Nevadskiy\Tokens\Token;

/** @var Factory $factory */
$factory->define(Token::class, function (Faker $faker) {
    return [
        'token' => Str::random(10),
        'type' => 'TOKEN_TYPE',
        'tokenable_id' => factory(User::class),
        'tokenable_type' => Nevadskiy\Tokens\Tests\Support\Models\User::class,
        'expired_at' => Carbon::now()->addMonth(),
        'used_at' => null,
    ];
});
