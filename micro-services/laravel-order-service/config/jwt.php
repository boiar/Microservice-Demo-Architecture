<?php

return [
    'secret' => env('JWT_SECRET', 'testing_secret_key'),
    'ttl' => env('JWT_TTL', 3600),
    'refresh_ttl' => env('JWT_REFRESH_TTL', 604800),
];
