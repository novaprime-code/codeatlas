# codeatlas/scanner

Project file discovery for CodeAtlas. Given a path, returns a `ProjectContext` with every analyzable file classified by type.

**Discovery only — no parsing.** Parsing happens in analyzers via the core parser.

## Components

| Component | Purpose |
|---|---|
| **Scanner** | The top-level composition — validates the project path, reads composer.json, detects the framework, walks configured paths, produces `ProjectContext` |
| **DirectoryWalker** | Lazy Symfony Finder traversal with configurable paths, extension filtering, and glob-based exclusions |
| **FileClassifier** | Pure prefix-based path → `FileType` mapping with custom overrides |
| **ComposerReader** | Extracts project name, PHP requirement, dependencies, and PSR-4 autoload map |
| **ComposerMetadata** | Immutable DTO for composer.json contents |
| **FrameworkDetector** | Identifies the framework (currently Laravel via `artisan` + `laravel/framework`) |
| **FrameworkResult** | Detection result carrying framework name and version |

## What gets discovered

Default scan paths (configurable via `ScanConfig`):
- `app/`, `routes/`, `config/`, `database/`, `bootstrap/`, `resources/`

Default exclusions:
- `vendor/`, `node_modules/`, `storage/`, `.git/`, `tests/`

Default file extensions:
- `.php` (which also matches `.blade.php` via suffix)

## Usage

```php
use CodeAtlas\Scanner\Scanner;

$scanner = Scanner::default();
$context = $scanner->scan('/path/to/laravel/project');

echo $context->name;                    // "vendor/project"
echo $context->framework;               // "laravel"
echo $context->frameworkVersion;        // "^11.0"

foreach ($context->filesOfType(FileType::Model) as $file) {
    // ...
}
```

## Installation

```bash
composer require codeatlas/scanner
```

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
