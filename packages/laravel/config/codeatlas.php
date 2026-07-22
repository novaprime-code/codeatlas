<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Scan Paths
    |--------------------------------------------------------------------------
    | Project-relative directories CodeAtlas will discover files in.
    | Null uses the built-in defaults (app, routes, config, database,
    | bootstrap, resources).
    */
    'scan_paths' => null,

    /*
    |--------------------------------------------------------------------------
    | Exclusions
    |--------------------------------------------------------------------------
    | Directory names or glob patterns to skip. Null uses the defaults
    | (vendor, node_modules, storage, .git, tests).
    */
    'exclude' => null,

    /*
    |--------------------------------------------------------------------------
    | Output Directory
    |--------------------------------------------------------------------------
    | Where exported analysis documents are written.
    */
    'output_path' => storage_path('codeatlas'),

    /*
    |--------------------------------------------------------------------------
    | Pretty Print
    |--------------------------------------------------------------------------
    | Human-readable JSON output. Disable for smaller files.
    */
    'pretty' => true,
];
