# CodeAtlas — Project Constitution

> Read this file completely before implementing anything. Every task, every PR, every line of code must conform to what's defined here. If something conflicts, this file wins.

## Identity

- **Project:** CodeAtlas — Visual Architecture Explorer
- **Codename:** Atlas
- **Tagline:** See your codebase. Understand your architecture.
- **Category:** Static analysis + architecture visualization (not runtime, not debugging)
- **First framework:** Laravel. Core is framework-agnostic.
- **License:** MIT
- **Minimum PHP:** 8.3
- **Minimum Node:** 20 LTS

## What CodeAtlas Is

CodeAtlas performs **static analysis** on a codebase and produces a **visual, interactive architecture map**. It discovers routes, controllers, services, repositories, models, events, jobs, policies, middleware, scheduled tasks, database schemas, and dependency graphs — then renders them as navigable, filterable, searchable graphs.

## What CodeAtlas Is NOT

- Not a runtime debugger (that's Telescope)
- Not a queue monitor (that's Horizon)
- Not a performance profiler (that's Pulse)
- Not a code quality tool (that's PHP Insights / PHPStan)
- CodeAtlas **never executes application code**
- CodeAtlas **never modifies application code**
- CodeAtlas is **read-only static analysis**

## The Pipeline (Sacred, Never Violate)

```
Source Code → Scanner → AST Parser → Analyzer → DTO → JSON → UI
```

Every component in the pipeline has exactly one job. Data flows in one direction. The UI never touches source code. Analyzers never touch the UI. JSON is the only contract between backend and frontend.

## Architecture Layers

```
codeatlas/contracts      → Interfaces everything implements
codeatlas/core           → Container, config, plugin loader, event bus, logger
codeatlas/scanner        → File/directory discovery (framework-agnostic)
codeatlas/laravel        → Laravel bridge (ServiceProvider, artisan commands, config)
codeatlas/analyzer-*     → One package per analysis domain
codeatlas/exporter-*     → One package per export format
@codeatlas/ui            → React + TypeScript frontend
@codeatlas/desktop       → Tauri desktop app
```

## Namespace Map

```
CodeAtlas\Contracts\         → packages/contracts/src/
CodeAtlas\Core\              → packages/core/src/
CodeAtlas\Scanner\           → packages/scanner/src/
CodeAtlas\Laravel\           → packages/laravel/src/
CodeAtlas\Analyzers\Routes\  → packages/analyzers/routes/src/
CodeAtlas\Analyzers\*\       → packages/analyzers/*/src/
CodeAtlas\Exporters\*\       → packages/exporters/*/src/
```

## Composer Package Names

| Package | Name |
|---------|------|
| Contracts | `codeatlas/contracts` |
| Core | `codeatlas/core` |
| Scanner | `codeatlas/scanner` |
| Laravel Bridge | `codeatlas/laravel` |
| Route Analyzer | `codeatlas/analyzer-routes` |
| Controller Analyzer | `codeatlas/analyzer-controllers` |
| Middleware Analyzer | `codeatlas/analyzer-middleware` |
| Service Analyzer | `codeatlas/analyzer-services` |
| Repository Analyzer | `codeatlas/analyzer-repositories` |
| Model Analyzer | `codeatlas/analyzer-models` |
| Event Analyzer | `codeatlas/analyzer-events` |
| Job Analyzer | `codeatlas/analyzer-jobs` |
| Policy Analyzer | `codeatlas/analyzer-policies` |
| Schedule Analyzer | `codeatlas/analyzer-schedule` |
| Notification Analyzer | `codeatlas/analyzer-notifications` |
| Cache Analyzer | `codeatlas/analyzer-cache` |
| Dependency Analyzer | `codeatlas/analyzer-dependencies` |
| JSON Exporter | `codeatlas/exporter-json` |
| Mermaid Exporter | `codeatlas/exporter-mermaid` |
| PlantUML Exporter | `codeatlas/exporter-plantuml` |

## NPM Package Names

| Package | Name |
|---------|------|
| UI | `@codeatlas/ui` |
| React Flow Nodes | `@codeatlas/react-flow-nodes` |
| Desktop | `@codeatlas/desktop` |

## Non-Negotiable Rules

1. **Never parse PHP with regex.** Always use `nikic/php-parser` for AST.
2. **Every analyzer is independent.** No analyzer may import from another analyzer.
3. **Every analyzer implements `AnalyzerInterface`.** No exceptions.
4. **Every analyzer outputs JSON conforming to the schema** defined in `JSON_SCHEMA.md`.
5. **The UI only consumes JSON.** It never sees PHP, never calls PHP functions.
6. **No framework-specific code in `core`, `contracts`, `scanner`, or analyzers.** Laravel-specific wiring lives only in `codeatlas/laravel`.
7. **Every package has Pest tests.** No package ships without tests.
8. **PHPStan level max** on every package.
9. **No `any` in TypeScript.** Strict mode, no exceptions.
10. **No Electron.** Desktop uses Tauri only.

## Current Phase

> **Phase 0 — Infrastructure**
> See ROADMAP.md and TASKS.md for details.

## Related Documents

- `ARCHITECTURE.md` — Detailed technical architecture
- `CODING_STANDARDS.md` — Code style, naming, patterns
- `JSON_SCHEMA.md` — JSON contract between backend and frontend
- `ROADMAP.md` — Phases and milestones
- `TASKS.md` — Current sprint and task backlog
- `UI_GUIDELINES.md` — Frontend design system
- `CONTRIBUTING.md` — Contribution workflow
