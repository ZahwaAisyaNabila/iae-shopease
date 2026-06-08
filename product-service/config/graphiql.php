<?php

declare(strict_types=1);

return [
    'routes' => [
        '/graphiql' => [
            'name' => 'graphiql',
            'endpoint' => '/graphql',
            'subscription-endpoint' => null,
        ],
    ],

    'enabled' => env('GRAPHIQL_ENABLED', true),
];
