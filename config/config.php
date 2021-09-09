<?php

return [
    // Supported: redis, none
    'default' => env('SERVICE_BROADCASTER_DRIVER', 'none'),

    'connection_prefix' => 'app-services_',

    'queue' => false,
];
