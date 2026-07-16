# CodeAtlas — Current Tasks

## Completed

### Sprint 0.1 — Repository Skeleton ✅
- ✅ All 12 tasks: monorepo, tooling, package skeletons, frontend, CI, git hooks

### Sprint 1.1 — Contracts Package ✅
- ✅ 33 source files (interfaces, enums, graph, value objects, exceptions)
- ✅ 15 test files
- ✅ Runtime verification: 34/34 assertions

### Sprint 1.2 — Core Engine ✅
- ✅ Container (DI, reflection auto-resolution, tagged bindings, circular detection) — **18/18 assertions**
- ✅ Config (dot-notation, deep merge, fromFile) — **24/24 assertions**
- ✅ EventBus + canonical Events constants — **8/8 assertions**
- ✅ PSR-3 Logger with pluggable sinks — **6/6 assertions**
- ✅ PhpParser wrapper with AST caching — **31/31 assertions** (caught grouped-use bug)
- ✅ PluginLoader with idempotent registration + auto-tagging — **7/7 assertions**
- ✅ PipelineRunner (Scanner → Analyzers → Exporters, event-driven, fault-isolated) — **20/20 assertions**
- ✅ 16 source files, 25 test files, 6 fixture directories
- ✅ End-to-end pipeline demonstrated with fake scanner + 3 analyzers + fake exporter

**Cumulative runtime verification across contracts + core: 148 assertions passing.**

---

## Active Phase: Phase 2 — Scanner Package

### Sprint 2.1 — File Discovery

### Task 2.1.1 — Directory walker with Symfony Finder ⬜
Create `CodeAtlas\Scanner\DirectoryWalker` using Symfony Finder with lazy iteration.

### Task 2.1.2 — Configurable scan paths + exclusions ⬜
Use `ScanConfig` from contracts. Support custom paths, glob exclusions, extension filters.

### Task 2.1.3 — File classification ⬜
Assign `FileType` based on path convention (`app/Http/Controllers/` → `Controller`, `app/Models/` → `Model`, etc.).

### Task 2.1.4 — Framework detection ⬜
Detect Laravel via `artisan` file + `composer.json` `laravel/framework` dependency.

### Task 2.1.5 — composer.json metadata parser ⬜
Extract project name, PHP version, Laravel version, PSR-4 autoload map.

### Task 2.1.6 — ProjectContext builder ⬜
Assemble the `ProjectContext` value object from walker + metadata.

### Task 2.1.7 — Fixture Laravel project ⬜
Create `tests/Fixtures/laravel-app/` with minimal but representative Laravel 11 structure.

### Task 2.1.8 — Full test suite + benchmarks ⬜
Cover: valid Laravel, empty dir, non-Laravel, custom paths, exclusions. Benchmark files/second on 100/500/1000/5000 file projects.

---

## Backlog (Sprint 3.1 — Route Analyzer)

- Implement AnalyzerInterface
- Parse routes/web.php, routes/api.php, routes/channels.php
- Extract URI, methods, name, controller, action, middleware, prefix, domain, where constraints
- Handle: closure routes, controller routes, resource routes, API resource routes, route groups
- Generate Route nodes and Route→Controller, Route→Middleware edges
- Fixture route files covering all styles
- Benchmark routes/second
