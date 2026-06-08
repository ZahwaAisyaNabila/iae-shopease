<?php

declare(strict_types=1);

return [
    'route_name' => 'graphql-playground',

    'route' => [
        'domain' => env('GRAPHQL_PLAYGROUND_DOMAIN', null),
    ],

    'endpoint' => 'graphql',

    'enabled' => env('GRAPHQL_PLAYGROUND_ENABLED', true),
];
