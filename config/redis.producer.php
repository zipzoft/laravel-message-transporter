<?php

use Illuminate\Support\Str;

return [
    'url' => env('REDIS_URL'),
    'host' => env('REDIS_HOST', '127.0.0.1'),
    'password' => env('REDIS_PASSWORD', null),
    'port' => env('REDIS_PORT', '6379'),
    'database' => 2,
    'options' => [
        'prefix' => env('SERVICE_BROADCASTER_PREFIX', strtolower(Str::slug(env('APP_NAME'), '_').'.'.env('APP_ENV')).'::'),
    ]
];
