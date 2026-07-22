# codeatlas/laravel

The Laravel bridge — the only package in CodeAtlas containing framework-specific code (constitution rule 6).

## Installation

```bash
composer require codeatlas/laravel
```

The service provider auto-registers via package discovery.

```bash
# Optional: publish the config
php artisan vendor:publish --tag=codeatlas-config
```

## Commands

```bash
# Full pipeline: scan → analyze → export JSON
php artisan codeatlas:analyze
php artisan codeatlas:analyze --analyzer=routes --output=/tmp/atlas --compact

# Discovery-only dry run (validates scan paths/exclusions)
php artisan codeatlas:scan
```

Output is written to `storage/codeatlas/codeatlas-analysis.json` by default (configurable via `codeatlas.output_path`).

## Architecture

The Laravel layer is deliberately thin:

| Class | Role |
|---|---|
| `CodeAtlasServiceProvider` | Merges config, publishes it, registers commands. Nothing else. |
| `AnalyzeCommand` / `ScanCommand` | Parse options, delegate, format output. |
| `CodeAtlasFactory` | **Framework-free** composition root: builds the core container, binds parser + scanner, registers plugins, returns a ready `PipelineRunner`. Any future Symfony bundle or CLI binary reuses this. |
| `AnalysisWriter` | **Framework-free** disk writer for exporter outputs. |

Project metadata (`name`, `framework`, versions) is injected into exports automatically by the `PipelineRunner` — commands don't assemble it.

## Config (`config/codeatlas.php`)

| Key | Default | Purpose |
|---|---|---|
| `scan_paths` | `null` (built-in defaults) | Directories to scan |
| `exclude` | `null` (built-in defaults) | Names/globs to skip |
| `output_path` | `storage_path('codeatlas')` | Export destination |
| `pretty` | `true` | Human-readable JSON |

## Testing

Integration tests use Orchestra Testbench (real Laravel skeleton boot):

```bash
composer require --dev orchestra/testbench
./vendor/bin/pest packages/laravel
```

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
