# CodeAtlas Architecture

## System Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      CodeAtlas Platform                      │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────┐   ┌─────────┐   ┌──────────┐   ┌──────────┐  │
│  │ Scanner  │──▶│  Parser │──▶│ Analyzer │──▶│ Exporter │  │
│  └──────────┘   └─────────┘   └──────────┘   └──────────┘  │
│       │                            │               │         │
│       │         ┌──────────┐       │               │         │
│       └────────▶│  Context │◀──────┘               │         │
│                 └──────────┘                       │         │
│                                                    ▼         │
│                                              ┌──────────┐   │
│                                              │   JSON    │   │
│                                              └──────────┘   │
│                                                    │         │
├─────────────────────────────────────────────────────────────┤
│                      JSON Boundary                           │
├─────────────────────────────────────────────────────────────┤
│                                                    │         │
│                                              ┌──────────┐   │
│                                              │    UI     │   │
│                                              └──────────┘   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## Package Dependency Graph

```
contracts (zero dependencies)
    ▲
    │
    ├── core (depends on: contracts)
    │     ▲
    │     │
    │     ├── scanner (depends on: contracts, core)
    │     │
    │     ├── analyzer-routes (depends on: contracts, core)
    │     ├── analyzer-controllers (depends on: contracts, core)
    │     ├── analyzer-services (depends on: contracts, core)
    │     ├── analyzer-* (depends on: contracts, core)
    │     │
    │     ├── exporter-json (depends on: contracts, core)
    │     ├── exporter-* (depends on: contracts, core)
    │     │
    │     └── laravel (depends on: contracts, core, scanner, analyzers, exporters)
    │
    └── (nothing depends on contracts alone except core)
```

**Critical rule:** Analyzers never depend on other analyzers. The dependency graph above is the only allowed dependency direction.

## Layer Details

### 1. Contracts (`codeatlas/contracts`)

Zero-dependency package. Contains only interfaces, abstract classes, enums, and value objects that define the public API of every other package.

Key interfaces:
- `AnalyzerInterface` — every analyzer implements this
- `ScannerInterface` — file discovery
- `ExporterInterface` — output generation
- `NodeInterface` — a graph node (entity)
- `EdgeInterface` — a graph edge (relationship)
- `GraphInterface` — collection of nodes + edges
- `ParserInterface` — AST parsing abstraction

Key value objects:
- `ProjectContext` — discovered project metadata
- `AnalysisResult` — output of an analyzer
- `FileReference` — a discovered file with path and type
- `NodeType` (enum) — route, controller, service, model, etc.
- `EdgeType` (enum) — uses, extends, implements, calls, etc.

### 2. Core (`codeatlas/core`)

The engine. No framework code. No Laravel. No Symfony.

Responsibilities:
- **Plugin Loader** — discovers and registers analyzers/exporters
- **Configuration** — loads and merges config from file/array
- **Container** — minimal DI container (not Laravel's, not Symfony's)
- **Event Bus** — internal event system for pipeline hooks
- **Logger** — PSR-3 compatible logging
- **Exception System** — typed exceptions for every failure mode
- **Pipeline Runner** — orchestrates scan → analyze → export
- **DTO Factory** — creates typed data objects from raw data

The core container is intentionally minimal. It supports:
- Binding interfaces to implementations
- Singleton registration
- Factory registration
- Tagged bindings (e.g., tag all analyzers)
- Auto-resolution via reflection

### 3. Scanner (`codeatlas/scanner`)

Discovers files and directories. Returns a `ProjectContext`. Does NOT parse files.

What it discovers:
- `routes/` — route definition files
- `app/Http/Controllers/` — controllers
- `app/Http/Middleware/` — middleware
- `app/Services/` — services (convention-based)
- `app/Repositories/` — repositories (convention-based)
- `app/Models/` — Eloquent models
- `app/Events/` — events
- `app/Listeners/` — listeners
- `app/Jobs/` — jobs
- `app/Policies/` — policies
- `app/Notifications/` — notifications
- `app/Console/` — commands and kernel
- `app/Providers/` — service providers
- `config/` — configuration files
- `database/migrations/` — migrations
- `database/factories/` — factories
- `database/seeders/` — seeders
- `resources/views/` — Blade templates
- `bootstrap/` — bootstrap files
- `artisan` — artisan entry point
- `composer.json` — package metadata

The scanner is configurable:
- Custom paths can be added
- Paths can be excluded via glob patterns
- File extensions are configurable

### 4. Parser

Not a standalone package — lives as a utility in `core`.

Wraps `nikic/php-parser` v5 to provide:
- AST generation from PHP source files
- Node traversal utilities
- Common extraction helpers (class name, methods, use statements, etc.)

The parser NEVER returns raw AST to analyzers. It returns structured DTOs.

### 5. Analyzers (`codeatlas/analyzer-*`)

Each analyzer is an independent package that:
1. Receives a `ProjectContext` (list of relevant files)
2. Uses the parser to read those files
3. Extracts domain-specific information
4. Returns an `AnalysisResult` containing `Node[]` and `Edge[]`

Analyzers MUST:
- Implement `AnalyzerInterface`
- Declare their `NodeType` and supported `EdgeType`s
- Return only DTOs, never raw AST
- Be stateless (no side effects, no file writes)
- Handle missing/malformed files gracefully (log, skip, continue)

Analyzers MUST NOT:
- Import from other analyzers
- Access the filesystem directly (use scanner's `ProjectContext`)
- Throw untyped exceptions
- Return partial results without indicating incompleteness

### 6. Laravel Bridge (`codeatlas/laravel`)

The only place Laravel-specific code exists. Provides:
- `CodeAtlasServiceProvider` — registers all services
- Artisan commands:
  - `codeatlas:scan` — run scanner
  - `codeatlas:analyze` — run specific or all analyzers
  - `codeatlas:export` — export analysis results
  - `codeatlas:serve` — start the web UI dev server
- Config file: `config/codeatlas.php`
- Route file: routes for the embedded web UI
- View: a single Blade view that mounts the React app

### 7. Exporters (`codeatlas/exporter-*`)

Each exporter takes an `AnalysisResult` and outputs a specific format:
- `exporter-json` — canonical JSON (required, always available)
- `exporter-mermaid` — Mermaid diagram syntax
- `exporter-plantuml` — PlantUML syntax
- `exporter-markdown` — Markdown documentation
- Future: PNG, SVG, PDF

### 8. UI (`@codeatlas/ui`)

React + TypeScript application. Consumes JSON only.

Layout:
```
┌──────────┬──────────────────────────────────┬───────────┐
│          │                                  │           │
│  Left    │         Center                   │  Right    │
│  Sidebar │         Graph Canvas             │  Inspector│
│          │         (React Flow)             │  Panel    │
│  - Routes│                                  │           │
│  - Ctrls │                                  │  Props    │
│  - Srvcs │                                  │  Code     │
│  - Repos │                                  │  Deps     │
│  - Models│                                  │  Notes    │
│  - Events│                                  │           │
│  - Jobs  │                                  │           │
│  - etc.  │                                  │           │
│          │                                  │           │
├──────────┴──────────────────────────────────┴───────────┤
│                    Console / Terminal                     │
└─────────────────────────────────────────────────────────┘
```

State management: Zustand
Data fetching: TanStack Query
Graphs: React Flow
Code display: Monaco Editor
Components: shadcn/ui
Styling: Tailwind CSS

### 9. Desktop (`@codeatlas/desktop`)

Tauri application wrapping the web UI. Provides:
- Native file system access (select project directory)
- Native menu bar
- Auto-updates
- OS notifications
- System tray

## Data Flow Example

**User clicks "Analyze" on a Laravel project:**

```
1. Laravel Bridge receives artisan command
2. Bridge calls Core Pipeline Runner
3. Pipeline Runner calls Scanner
4. Scanner walks filesystem, returns ProjectContext
5. Pipeline Runner iterates registered Analyzers
6. Each Analyzer receives ProjectContext
7. Each Analyzer parses relevant files via Parser
8. Each Analyzer returns AnalysisResult (Nodes + Edges)
9. Pipeline Runner merges all AnalysisResults into a Graph
10. Pipeline Runner calls Exporter (JSON)
11. JSON is written to disk or served via HTTP
12. UI fetches JSON
13. UI renders Graph using React Flow
```

## Error Handling Strategy

Every layer has typed exceptions:

```
CodeAtlas\Contracts\Exceptions\
    CodeAtlasException (base)
    ├── ScannerException
    ├── ParserException
    ├── AnalyzerException
    ├── ExporterException
    ├── ConfigurationException
    └── PluginException
```

Analyzers MUST catch parsing errors and continue. A malformed file must not crash the entire analysis. Instead:
- Log a warning
- Mark the file as `skipped` in the result
- Continue with remaining files

## Performance Considerations

- Scanner uses `Symfony\Finder` with lazy iterators
- Parser caches AST per file (keyed by file hash)
- Analyzers run in parallel when possible (future)
- Large projects: stream results instead of building full graph in memory
- UI: virtualize node lists, lazy-render off-screen graph sections
- Benchmarks required for every analyzer (files/second metric)

## Security

- CodeAtlas never executes analyzed code
- CodeAtlas never sends code to external services
- All analysis is local
- No network calls during analysis
- File access is read-only and scoped to the project directory
