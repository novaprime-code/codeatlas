# codeatlas/contracts

Interfaces, enums, and value objects that define the CodeAtlas plugin API.

**Zero runtime dependencies.** Every other CodeAtlas package implements these contracts.

## API surface

### Core interfaces

| Interface | Role |
|---|---|
| `AnalyzerInterface` | Every analyzer implements this — receives a `ProjectContext`, returns an `AnalysisResult` |
| `ScannerInterface` | File discovery — walks a project path, returns a `ProjectContext` |
| `ExporterInterface` | Output generation — turns an `AnalysisResult` into an `ExportOutput` |
| `ParserInterface` / `ParsedFileInterface` | AST parsing abstraction (concrete impl lives in `codeatlas/core`) |
| `PluginInterface` | Entry point for a plugin package to register its services |
| `ContainerInterface` | Minimal DI container contract |
| `ConfigInterface` | Read access to CodeAtlas configuration with dot-notation |

### Enums

| Enum | Cases | Purpose |
|---|---|---|
| `NodeType` | 23 | Every entity a graph can contain (route, controller, model, event, ...) |
| `EdgeType` | 15 | Every relationship (routes_to, calls, depends_on, has_relationship, ...) |
| `FileType` | 19 | Classification assigned by the scanner |
| `Severity` | 4 | Error, Warning, Info, Debug |

### Graph primitives (`CodeAtlas\Contracts\Graph`)

- `NodeInterface` + `Node` — immutable graph node with deterministic ID
- `EdgeInterface` + `Edge` — immutable directed edge with deterministic ID
- `GraphInterface` + `Graph` — collection with ID-based deduplication and idempotent merge

### Value objects (`CodeAtlas\Contracts\ValueObjects`)

- `FileReference` — discovered file with path, type, line range
- `ScanConfig` — scanner configuration (paths, exclusions, extensions)
- `ProjectContext` — output of a scanner run, input to every analyzer
- `AnalysisResult` — output of an analyzer run
- `AnalysisError` — non-fatal per-file issue
- `ExportConfig` / `ExportOutput` — exporter input and output

### Exception hierarchy (`CodeAtlas\Contracts\Exceptions`)

```
CodeAtlasException (base)
├── ScannerException
├── ParserException
├── AnalyzerException
├── ExporterException
├── ConfigurationException
├── PluginException
└── ContainerException
```

Every concrete exception ships named constructors (`::pathNotFound`, `::syntaxError`, `::circularDependency`, ...) so consumers throw with intent rather than raw strings.

## Design rules enforced here

- Every value object is a `final readonly class`
- Every graph implementation deduplicates by ID (first-write-wins)
- Every exception is typed and unrecoverable-only — non-fatal issues become `AnalysisError` values
- No framework code — this package will never depend on Laravel, Symfony HTTP, or anything else framework-specific

## Installation

```bash
composer require codeatlas/contracts
```

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
