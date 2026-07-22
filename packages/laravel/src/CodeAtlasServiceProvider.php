<?php

declare(strict_types=1);

namespace CodeAtlas\Laravel;

use CodeAtlas\Laravel\Commands\AnalyzeCommand;
use CodeAtlas\Laravel\Commands\ScanCommand;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel entry point for CodeAtlas.
 *
 * Deliberately thin: it merges config, exposes the publishable config
 * file, and registers the artisan commands. All engine wiring lives in
 * the framework-free CodeAtlasFactory, which commands call directly.
 */
final class CodeAtlasServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/codeatlas.php', 'codeatlas');
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/codeatlas.php' => config_path('codeatlas.php'),
        ], 'codeatlas-config');

        $this->commands([
            AnalyzeCommand::class,
            ScanCommand::class,
        ]);
    }
}
