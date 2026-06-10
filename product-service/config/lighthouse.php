<?php

declare(strict_types=1);

return [
    'route' => [
        'uri' => '/graphql',
        'name' => 'graphql',
        'middleware' => [
            Nuwave\Lighthouse\Http\Middleware\AcceptJson::class,
            Nuwave\Lighthouse\Http\Middleware\AttemptAuthentication::class,
        ],
    ],

    'guards' => null,
    'schema_path' => base_path('graphql/schema.graphql'),
    'schema_cache' => [
        'enable' => env('LIGHTHOUSE_SCHEMA_CACHE_ENABLE', env('APP_ENV') !== 'local'),
        'path' => env('LIGHTHOUSE_SCHEMA_CACHE_PATH', base_path('bootstrap/cache/lighthouse-schema.php')),
    ],

    'query_cache' => [
        'enable' => false, // UBAH DARI true KE false
        'store' => env('LIGHTHOUSE_QUERY_CACHE_STORE', null),
        'ttl' => env('LIGHTHOUSE_QUERY_CACHE_TTL', null),
    ],
];
