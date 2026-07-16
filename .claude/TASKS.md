# CodeAtlas — Current Tasks

> This file tracks the active sprint. Update after every completed task.

## Completed

### Sprint 0.1 — Repository Skeleton ✅
- ✅ Task 0.1.1 Root Repository Setup
- ✅ Task 0.1.2 PHP Tooling Configuration
- ✅ Task 0.1.3 Contracts Package Skeleton
- ✅ Task 0.1.4 Core Package Skeleton
- ✅ Task 0.1.5 Scanner Package Skeleton
- ✅ Task 0.1.6 Laravel Bridge Package Skeleton
- ✅ Task 0.1.7 Route Analyzer Package Skeleton
- ✅ Task 0.1.8 JSON Exporter Package Skeleton
- ✅ Task 0.1.9 Frontend Skeleton (Vite + React + TS + Tailwind + React Flow + Monaco + TanStack Query + Zustand)
- ✅ Task 0.1.10 CI Pipeline (GitHub Actions, PHP 8.3 & 8.4 matrix + frontend)
- ✅ Task 0.1.11 Monorepo Orchestration (Turbo + Makefile)
- ✅ Task 0.1.12 Git Hooks + Commit Enforcement (Husky + lint-staged + Commitlint)

### Sprint 1.1 — Contracts Package ✅
- ✅ AnalyzerInterface, ScannerInterface, ExporterInterface, ParserInterface, ParsedFileInterface
- ✅ PluginInterface, ContainerInterface, ConfigInterface
- ✅ NodeType (23 cases), EdgeType (15 cases), FileType (19 cases), Severity (4 cases)
- ✅ NodeInterface + Node, EdgeInterface + Edge, GraphInterface + Graph
- ✅ FileReference, ScanConfig, ProjectContext
- ✅ AnalysisResult, AnalysisError
- ✅ ExportConfig, ExportOutput
- ✅ Full exception hierarchy (8 classes)
- ✅ 15 test files covering enums, graph primitives, value objects, exceptions
- ✅ Runtime verification: 34/34 assertions passing

## Active Phase: Phase 1 — Core Package

### Sprint 1.2 — Core Engine

**Priority Order:**

### Task 1.2.1 — Minimal DI Container ⬜
Create `CodeAtlas\Core\Container` implementing `ContainerInterface`.

Support:
- `bind(string, string|callable)`
- `singleton(string, string|callable)`
- `make(string): object` with reflection-based auto-resolution
- `has(string): bool`
- `tag(string, string)` / `tagged(string): array`
- Circular dependency detection → `ContainerException`

Acceptance:
- [ ] All ContainerInterface methods implemented
- [ ] Auto-resolution walks type-hinted constructor params
- [ ] Singletons return the same instance
- [ ] Tagged bindings group correctly
- [ ] PHPStan level max passes
- [ ] Test coverage > 90%

### Task 1.2.2 — Configuration Loader ⬜
Create `CodeAtlas\Core\Config` implementing `ConfigInterface`.

Support:
- `fromArray(array): self`
- `fromFile(string): self`
- `merge(self): self`
- Dot-notation `get('scanner.paths')`
- Default values on missing keys

### Task 1.2.3 — Plugin Loader ⬜
Create `CodeAtlas\Core\PluginLoader`. Discover and register plugins via:
- Explicit `register(class-string)` calls
- Directory scanning
- Composer `extra.codeatlas.plugins`

### Task 1.2.4 — Event Bus ⬜
`CodeAtlas\Core\EventBus` with `listen()` / `dispatch()`.

### Task 1.2.5 — PSR-3 Logger ⬜
File + console handlers, level filtering, PSR-3 compliant.

### Task 1.2.6 — Parser Wrapper ⬜
`CodeAtlas\Core\PhpParser` implementing `ParserInterface`. Wraps nikic/php-parser v5 with AST caching keyed by file hash.

### Task 1.2.7 — Pipeline Runner ⬜
`CodeAtlas\Core\PipelineRunner` orchestrating Scanner → Analyzers → Exporters.

### Task 1.2.8 — Core tests + benchmarks ⬜

---

## Backlog (Sprint 1.3 — Scanner)

- Directory walker with Symfony Finder
- Configurable paths and exclusions
- File classification
- Framework detection
- composer.json metadata parser
- ProjectContext builder
- Fixture Laravel project
- Full test suite + benchmarks
