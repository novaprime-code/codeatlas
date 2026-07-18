# codeatlas/core

The CodeAtlas engine. Framework-agnostic by design — no Laravel, no Symfony framework code.

## Components

| Component | Purpose |
|---|---|
| **Container** | Minimal DI with reflection-based auto-resolution, tagged bindings, and circular-dependency detection |
| **Config** | Array/file loading, dot-notation access (`scanner.paths`), deep merge |
| **EventBus** | Sync in-process pipeline events + `Events` constants (`scan.started`, `analysis.error`, etc.) |
| **Logger** | PSR-3 compliant with level filtering, `{placeholder}` interpolation, and pluggable sinks (stderr, file, null) |
| **PhpParser** | Wrapper around `nikic/php-parser` v5 with content-hash caching; returns typed `ParsedFile` DTOs |
| **ParsedFile** | Immutable AST result exposing `namespace()`, `useStatements()`, `classNames()`, `resolveClassName()`, `findNodes()` |
| **PluginLoader** | Discovers plugins via explicit registration, wires them into the container, and tags analyzers/exporters |
| **PipelineRunner** | Orchestrates Scanner → Analyzers → Exporters, dispatches lifecycle events, isolates analyzer failures |

## The pipeline (constitution §"The Pipeline")

```
ProjectContext → tagged analyzers → merged Graph → tagged exporters → PipelineResult
```

A failing analyzer becomes an `AnalysisError` in the final result — the remaining analyzers keep running. This is a hard invariant, not a soft one.

## Installation

```bash
composer require codeatlas/core
```

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
