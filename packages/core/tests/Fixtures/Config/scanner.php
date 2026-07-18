<?php

declare(strict_types=1);

return [
    'scanner' => [
        'paths' => ['app', 'routes', 'config'],
        'exclude' => ['vendor', 'node_modules'],
    ],
    'analyzers' => [
        'routes' => true,
        'controllers' => false,
    ],
];
