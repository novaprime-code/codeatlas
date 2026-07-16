# Changelog

All notable changes to CodeAtlas will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added — Phase 1 Core (Sprint 1.2)

- **Container** (`CodeAtlas\Core\Container`): reflection-based auto-resolution, tagged bindings, singleton/instance/factory registration, circular dependency detection
- **Config** (`CodeAtlas\Core\Config`): array + PHP file loading, deep merge, dot-notation get/set/has
- **EventBus** (`CodeAtlas\Core\Events`): sync in-process events with registration-order dispatch + canonical `Events` constants
- **Logger** (`CodeAtlas\Core\Logging`): PSR-3 compliant, level filtering, `{placeholder}` interpolation, pluggable `LoggerSink` (stderr, file, null)
- **PhpParser** (`CodeAtlas\Core\Parser`): `nikic/php-parser` v5 wrapper with content-hash caching
- **ParsedFile**: immutable AST result with namespace, use statement extraction (including grouped uses), class-like discovery, FQCN resolution, and `findNodes()` traversal helper
- **PluginLoader** (`CodeAtlas\Core\Plugin`): idempotent plugin registration, automatic analyzer/exporter tagging
- **PipelineRunner** (`CodeAtlas\Core\Pipeline`): full Scanner → Analyzers → Exporters orchestration with event dispatch, analyzer failure isolation, and per-run duration tracking
- **PipelineResult**: value object carrying context, per-analyzer results, merged Graph, exports, and metadata

### Added — Phase 1 Contracts (Sprint 1.1)

- 8 interfaces, 4 enums, 6 graph primitives, 7 value objects, 8-class exception hierarchy
- 15 test files

### Added — Phase 0 Infrastructure (Sprint 0.1)

- Monorepo scaffolding (Composer path repos + PNPM workspaces + Turborepo)
- All 6 PHP package skeletons
- Frontend skeleton with strict TypeScript, Tailwind, React Flow, Monaco, TanStack Query, Zustand
- Tooling: Pint (PER), PHPStan level max, Rector PHP 8.3, Pest
- CI matrix (PHP 8.3 + 8.4), Husky + lint-staged, Commitlint, project management setup
