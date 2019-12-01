<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Token Options
    |--------------------------------------------------------------------------
    | * TTL - is the token time to live in minutes
    | * previous - is the token generation strategy. Can be one of ['remove', 'reuse', 'keep']
    | * generation_throttling - determine if the throttling enabled for the token generation process
    | * generation_attempts - how many attempts per client are available for generation attempt of the same token type
    | * generation_attempts_interval - the interval which determines how many generation attempts can be process within
    | * usage_throttling - determine if the throttling enabled for the token usage process
    | * usage_attempts - how many attempts per client are available for usage attempt of the same token type
    | * usage_attempts_interval - the interval which determines how many usage attempts can be process within
    | * generator - the generation class for generating token strings
    */
    'defaults' => [
        'ttl' => 43200, // minutes in month (60 * 24 * 30)
        'previous' => 'remove',
        'generation_throttling' => true,
        'generation_attempts' => 3,
        'generation_attempts_interval' => 10,
        'usage_throttling' => true,
        'usage_attempts' => 5,
        'usage_attempts_interval' => 10,
        'generator' => Nevadskiy\Tokens\Generator\RandomHashGenerator::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tokens database table name
    |--------------------------------------------------------------------------
    */
    'table' => 'tokens',
];
