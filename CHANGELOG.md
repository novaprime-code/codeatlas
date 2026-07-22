# Changelog

All notable changes to CodeAtlas will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added — Phase 3 JSON Exporter (Sprint 3.2)

- **JsonExporter** (`CodeAtlas\Exporters\Json`): the canonical exporter producing the complete JSON_SCHEMA.md document — `$schema`, `version`, `project`, `analysis`, `graph`, `results`, `errors`
- Schema version stamping (`1.0.0`) on every export
- `prettyPrint` honoured from `ExportConfig`; compact output for production
- Project metadata supplied via `ExportConfig::$options['project']` (the Laravel bridge maps this from `ProjectContext`)
- Merged pipeline results (`analyzer === 'pipeline'`) pass through as the per-analyzer map; single-analyzer results wrapped under their own name
- Empty-map normalization: empty `metadata`/`where` maps serialize as `{}` instead of PHP's default `[]`, keeping TypeScript `Record<string, T>` types parseable
- **JsonExporterPlugin** for container registration
- Round-trip integration test: real Scanner → Parser → RouteAnalyzer → JsonExporter → decode → schema assertions

**The backend pipeline is complete: Source → Scanner → AST → Analyzer → DTO → JSON.**

### Added — Phase 3 Route Analyzer (Sprint 3.1)
- RouteAnalyzer with full group/resource/closure support, fault isolation, graph output

### Added — Phase 2 Scanner (Sprint 2.1)
- Scanner, DirectoryWalker, FileClassifier, ComposerReader, FrameworkDetector

### Added — Phase 1 Core (Sprint 1.2)
- Container, Config, EventBus, Logger, PhpParser, PluginLoader, PipelineRunner

### Added — Phase 1 Contracts (Sprint 1.1)
- Interfaces, enums, graph primitives, value objects, exception hierarchy

### Added — Phase 0 Infrastructure (Sprint 0.1)
- Monorepo scaffolding, tooling, CI, git hooks, project management setup
