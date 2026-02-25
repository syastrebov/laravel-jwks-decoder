<?php

declare(strict_types=1);

return [
    'url' => env('JWKS_URL', 'http://localhost/.well-known/jwks.json'),
    'cache_ttl' => (int) env('JWKS_CACHE_TTL', 86400),
    'cache_key' => 'auth_jwks_keys',
];
