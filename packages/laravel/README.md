# codeatlas/laravel

Laravel integration for CodeAtlas. The **only** package containing Laravel-specific code.

## Provides

- `CodeAtlasServiceProvider` — auto-discovered
- Artisan commands: `codeatlas:scan`, `codeatlas:analyze`, `codeatlas:export`
- Publishable config: `config/codeatlas.php`
- Embedded web UI at `/codeatlas`

## Installation

```bash
composer require codeatlas/laravel --dev
php artisan vendor:publish --tag=codeatlas-config
php artisan codeatlas:analyze
```

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
