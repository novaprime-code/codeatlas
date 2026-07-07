<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Scan Paths
    |--------------------------------------------------------------------------
    | Directories to scan, relative to the project root.
    */
    'scan_paths' => [
        'app',
        'routes',
        'config',
        'database',
        'bootstrap',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude Paths
    |--------------------------------------------------------------------------
    | Glob patterns to exclude from scanning.
    */
    'exclude_paths' => [
        'vendor',
        'node_modules',
        'storage',
        'tests',
    ],

    /*
    |--------------------------------------------------------------------------
    | Analyzers
    |--------------------------------------------------------------------------
    | Enable or disable individual analyzers.
    */
    'analyzers' => [
        'routes' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */
    'export_path' => storage_path('codeatlas'),

    /*
    |--------------------------------------------------------------------------
    | Web UI
    |--------------------------------------------------------------------------
    */
    'ui_enabled' => env('CODEATLAS_UI_ENABLED', true),
    'ui_route_prefix' => env('CODEATLAS_UI_PREFIX', 'codeatlas'),
    'ui_middleware' => ['web'],

];
