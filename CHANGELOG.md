# Changelog

All notable changes to CodeAtlas will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added — Phase 2 Scanner (Sprint 2.1)

- **Scanner** (`CodeAtlas\Scanner\Scanner`): top-level file discovery with path validation, ProjectContext assembly, and a `Scanner::default()` factory
- **DirectoryWalker** (`CodeAtlas\Scanner\Discovery`): lazy Symfony Finder traversal with configurable paths, glob-based exclusions, and extension filtering
- **FileClassifier** (`CodeAtlas\Scanner\Classification`): pure prefix-based path → FileType classification with support for custom overrides
- **ComposerReader** + **ComposerMetadata** (`CodeAtlas\Scanner\Framework`): safe composer.json parsing with `tryRead` (missing = null) and `read` (missing/malformed = ScannerException)
- **FrameworkDetector** + **FrameworkResult**: Laravel detection requires both `artisan` file AND `laravel/framework` in composer dependencies
- Three fixture projects (minimal Laravel 11, empty, non-Laravel) with 22 discoverable files total
- 5 test files covering classification, composer parsing, framework detection, and end-to-end scanner behaviour

### Changed

- `CodeAtlas\Contracts\ValueObjects\ScanConfig::default()` now includes `resources` in scan paths, per ARCHITECTURE.md's discovery list — Blade views were previously missed
- `CodeAtlas\Contracts\Exceptions\ScannerException` gains `composerNotReadable()` and `composerInvalidJson()` named constructors

### Added — Phase 1 Core (Sprint 1.2)
- Container, Config, EventBus, Logger, PhpParser (with grouped-use support), PluginLoader, PipelineRunner
- 25 test files across 8 subsystems

### Added — Phase 1 Contracts (Sprint 1.1)
- 8 interfaces, 4 enums, 6 graph primitives, 7 value objects, 8-class exception hierarchy

### Added — Phase 0 Infrastructure (Sprint 0.1)
- Full monorepo scaffolding, tooling, CI, git hooks, project management setup
