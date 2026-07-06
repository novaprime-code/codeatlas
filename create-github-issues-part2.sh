#!/bin/bash

# =============================================================================
# CodeAtlas — GitHub Issues Creator (Part 2: Phases 1-11)
# =============================================================================
# Run AFTER create-github-issues.sh (Part 1: Phase 0 + Contracts)
# Usage: ./create-github-issues-part2.sh YOUR_ORG/codeatlas
# =============================================================================

REPO="${1:-YOUR_ORG/codeatlas}"

create_issue() {
    local title="$1"
    local body="$2"
    local labels="$3"
    local milestone="$4"
    echo "Creating: $title"
    gh issue create --repo "$REPO" --title "$title" --body "$body" --label "$labels" --milestone "$milestone" 2>/dev/null
    sleep 1
}

# =============================================================================
# PHASE 1 — CORE PACKAGE
# =============================================================================

echo ""
echo "=== Phase 1 — Core Package ==="
echo ""

create_issue \
    "[Epic] Phase 1 — Core Package" \
    "## Overview
Engine implementation: container, config, plugin loader, event bus, logger, pipeline runner, parser wrapper.

## Depends On
- All contracts from Phase 1 Contracts epic

## Acceptance Criteria
- [ ] Container resolves dependencies via reflection
- [ ] Config loads from array and file with dot-notation
- [ ] Plugin loader auto-discovers analyzers/exporters
- [ ] Event bus dispatches sync events
- [ ] Pipeline runner orchestrates scan → analyze → export
- [ ] Parser wraps nikic/php-parser with typed DTOs
- [ ] 90%+ test coverage
- [ ] Benchmarks established" \
    "type:epic,phase:1-core,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement minimal DI Container" \
    "## Description
Create \`CodeAtlas\Core\Container\` class. No Laravel or Symfony dependency.

Support:
- \`bind(string \$abstract, string|callable \$concrete)\`
- \`singleton(string \$abstract, string|callable \$concrete)\`
- \`factory(string \$abstract, callable \$factory)\`
- \`make(string \$abstract): object\`
- \`has(string \$abstract): bool\`
- \`tagged(string \$tag): array\`
- \`tag(string \$abstract, string \$tag)\`
- Auto-resolution via PHP reflection (resolve constructor params)

## Acceptance Criteria
- [ ] Bind and resolve classes
- [ ] Singletons return same instance
- [ ] Tagged bindings group related services
- [ ] Auto-resolution works for type-hinted constructors
- [ ] Circular dependency detection throws \`ContainerException\`
- [ ] Tests pass, PHPStan passes" \
    "type:task,phase:1-core,pkg:core,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement Configuration loader" \
    "## Description
Create \`CodeAtlas\Core\Config\` implementing \`ConfigInterface\`.

Support:
- Load from associative array
- Load from PHP file (returns array)
- Merge multiple configs (later overrides earlier)
- Dot-notation access: \`\$config->get('scanner.paths')\`
- Default values: \`\$config->get('missing.key', 'default')\`

## Acceptance Criteria
- [ ] Array loading works
- [ ] File loading works
- [ ] Merge works (deep merge)
- [ ] Dot-notation resolves nested keys
- [ ] Default values returned for missing keys
- [ ] \`has()\` checks existence correctly
- [ ] Tests pass" \
    "type:task,phase:1-core,pkg:core,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement Plugin Loader" \
    "## Description
Create \`CodeAtlas\Core\PluginLoader\` that discovers and registers plugins (analyzers and exporters).

Discovery methods:
1. Explicit registration: \`\$loader->register(RouteAnalyzer::class)\`
2. Directory scanning: scan a directory for classes implementing \`PluginInterface\`
3. Composer extra: read \`extra.codeatlas.plugins\` from composer.json

## Acceptance Criteria
- [ ] Explicit registration works
- [ ] Directory scanning discovers plugins
- [ ] Plugins are registered into Container with correct tags
- [ ] Invalid plugins throw \`PluginException\`
- [ ] Tests pass" \
    "type:task,phase:1-core,pkg:core,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement Event Bus" \
    "## Description
Create \`CodeAtlas\Core\EventBus\` for internal sync events.

Methods:
- \`listen(string \$event, callable \$handler): void\`
- \`dispatch(string \$event, mixed \$payload = null): void\`

Built-in events:
- \`scan.started\`, \`scan.completed\`
- \`analysis.started\`, \`analysis.completed\`, \`analysis.error\`
- \`export.started\`, \`export.completed\`
- \`pipeline.started\`, \`pipeline.completed\`

## Acceptance Criteria
- [ ] Listeners fire in registration order
- [ ] Multiple listeners per event
- [ ] Payload passed to handlers
- [ ] No event = no error (silent)
- [ ] Tests pass" \
    "type:task,phase:1-core,pkg:core,P2-medium" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement PSR-3 Logger" \
    "## Description
Create \`CodeAtlas\Core\Logger\` implementing \`Psr\Log\LoggerInterface\`.

Support:
- File output (append to log file)
- Console output (stderr)
- Configurable minimum level
- Formatted output: \`[2026-07-05 14:30:00] [WARNING] Message {context}\`

## Acceptance Criteria
- [ ] PSR-3 compliant
- [ ] File and console handlers work
- [ ] Level filtering works
- [ ] Context interpolation works
- [ ] Tests pass" \
    "type:task,phase:1-core,pkg:core,P2-medium" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement Pipeline Runner" \
    "## Description
Create \`CodeAtlas\Core\PipelineRunner\` that orchestrates the full analysis flow.

Flow:
1. Run Scanner → get ProjectContext
2. Iterate registered Analyzers → collect AnalysisResult[]
3. Merge all results into a single Graph
4. Run Exporter(s) on merged result
5. Dispatch events at each stage

Accept config to filter which analyzers run.

## Acceptance Criteria
- [ ] Full pipeline runs: scan → analyze → export
- [ ] Analyzer filtering works (run only selected)
- [ ] Results merge into single Graph correctly
- [ ] Events dispatched at each stage
- [ ] Errors in one analyzer don't crash others
- [ ] Tests pass with mock analyzers" \
    "type:task,phase:1-core,pkg:core,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement Parser wrapper (nikic/php-parser)" \
    "## Description
Create \`CodeAtlas\Core\PhpParser\` implementing \`ParserInterface\`.

Wrap nikic/php-parser v5. Return \`ParsedFile\` DTO with:
- Full AST
- Helper methods: \`getClasses()\`, \`getMethods(string \$class)\`, \`getUseStatements()\`, \`getNamespace()\`, \`getClassConstants()\`, \`getProperties()\`
- AST cache keyed by file content hash (md5)

## Acceptance Criteria
- [ ] Parses valid PHP files and returns typed DTOs
- [ ] Helper methods extract correct data
- [ ] Caching works (same file = no re-parse)
- [ ] Malformed files throw \`ParserException\`, never crash
- [ ] Tests pass with fixture PHP files
- [ ] Benchmark: files/second" \
    "type:task,phase:1-core,pkg:core,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement DTO factory and common extractors" \
    "## Description
Create \`CodeAtlas\Core\DtoFactory\` for building typed DTOs from AST nodes.

Common extractors:
- Class name from AST node
- FQCN resolution (use statements + namespace)
- Method signature extraction (params, return type, visibility)
- Property extraction (name, type, default, visibility)
- Attribute extraction (PHP 8 attributes)

## Acceptance Criteria
- [ ] Factory produces correct DTOs from AST
- [ ] FQCN resolution handles aliases and grouped imports
- [ ] Method signatures include all parameter details
- [ ] Tests pass" \
    "type:task,phase:1-core,pkg:core,P2-medium" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write comprehensive core tests" \
    "## Description
Full test suite for all core components.

Coverage targets:
- Container: bind, resolve, singleton, tagged, auto-resolution, circular deps
- Config: load, merge, dot-notation, defaults, missing keys
- Pipeline: full flow with mocks, error handling, filtering
- Parser: valid files, invalid files, caching, all helper methods
- EventBus: listen, dispatch, ordering, payloads
- Logger: levels, handlers, formatting

## Acceptance Criteria
- [ ] 90%+ line coverage
- [ ] All edge cases tested
- [ ] PHPStan passes" \
    "type:test,phase:1-core,pkg:core,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write core benchmarks" \
    "## Description
PHPBench benchmarks for:
- Container: resolution speed (1000 resolves)
- Parser: files/second (parse 100 PHP files)
- Pipeline: end-to-end with mock analyzers

## Acceptance Criteria
- [ ] Benchmarks run and produce metrics
- [ ] Baseline numbers documented" \
    "type:perf,phase:1-core,pkg:core,P2-medium" \
    "v0.1.0 — First Light"

# =============================================================================
# PHASE 2 — SCANNER
# =============================================================================

echo ""
echo "=== Phase 2 — Scanner ==="
echo ""

create_issue \
    "[Epic] Phase 2 — Scanner" \
    "## Overview
File and directory discovery. Returns ProjectContext. No parsing, only discovery.

## Depends On
- Core package (Phase 1)

## Acceptance Criteria
- [ ] Discovers all standard Laravel directories
- [ ] Configurable paths and exclusions
- [ ] Classifies files by type
- [ ] Detects Laravel framework
- [ ] Handles missing dirs gracefully
- [ ] Benchmarked on large projects" \
    "type:epic,phase:2-scanner,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement directory walker with Symfony Finder" \
    "## Description
Create \`CodeAtlas\Scanner\DirectoryWalker\` using Symfony Finder.

- Lazy iteration (no loading entire tree into memory)
- Walk configurable base paths
- Return \`FileReference\` objects
- Respect exclusion patterns

## Acceptance Criteria
- [ ] Walks directories recursively
- [ ] Returns FileReference with path, type, absolutePath
- [ ] Lazy (memory efficient)
- [ ] Tests pass" \
    "type:task,phase:2-scanner,pkg:scanner,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement configurable scan paths" \
    "## Description
Accept custom paths config via \`ScanConfig\`.

Default Laravel paths:
\`app/\`, \`routes/\`, \`config/\`, \`database/\`, \`resources/\`, \`bootstrap/\`

Support:
- Adding custom paths
- Removing default paths
- Relative paths resolved from project root

## Acceptance Criteria
- [ ] Default paths match Laravel convention
- [ ] Custom paths override/extend defaults
- [ ] Relative paths resolve correctly
- [ ] Tests pass" \
    "type:task,phase:2-scanner,pkg:scanner,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement glob-based exclusion patterns" \
    "## Description
Support excluding paths via glob patterns.

Default exclusions: \`vendor/\`, \`node_modules/\`, \`storage/\`, \`.git/\`, \`tests/\`, \`public/\`

Configurable via ScanConfig.

## Acceptance Criteria
- [ ] Default exclusions applied
- [ ] Custom exclusions work
- [ ] Glob patterns match correctly
- [ ] Tests pass" \
    "type:task,phase:2-scanner,pkg:scanner,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement file classification by path convention" \
    "## Description
Classify files into FileType based on directory path:
- \`app/Http/Controllers/\` → FileType::Controller
- \`app/Models/\` → FileType::Model
- \`routes/\` → FileType::Route
- \`app/Services/\` → FileType::Service
- \`app/Events/\` → FileType::Event
- etc. (full mapping in ARCHITECTURE.md)

## Acceptance Criteria
- [ ] All standard Laravel paths map to correct FileType
- [ ] Unknown paths default to FileType::Other
- [ ] Classification is configurable (custom path→type mappings)
- [ ] Tests pass" \
    "type:task,phase:2-scanner,pkg:scanner,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement framework detection" \
    "## Description
Detect if target directory is a Laravel project:
1. Check for \`artisan\` file
2. Check \`composer.json\` for \`laravel/framework\` dependency
3. Check for \`app/\` + \`routes/\` structure

Return framework name and version in ProjectContext.

## Acceptance Criteria
- [ ] Laravel projects detected correctly
- [ ] Non-Laravel directories return warning/null
- [ ] Framework version extracted from composer.json
- [ ] Tests pass" \
    "type:task,phase:2-scanner,pkg:scanner,P2-medium" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement composer.json parser for metadata" \
    "## Description
Read project's \`composer.json\` to extract:
- Project name
- PHP version requirement
- Laravel version
- PSR-4 autoload mappings (for FQCN resolution in analyzers)

## Acceptance Criteria
- [ ] Metadata extracted correctly
- [ ] Missing composer.json handled gracefully
- [ ] Autoload mappings available in ProjectContext
- [ ] Tests pass" \
    "type:task,phase:2-scanner,pkg:scanner,P2-medium" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement ProjectContext builder" \
    "## Description
Assemble complete \`ProjectContext\` from walker results + metadata:
- All discovered files as FileReference[]
- File counts per FileType
- Framework info
- Autoload mappings
- \`filesOfType(FileType)\` filter method

## Acceptance Criteria
- [ ] ProjectContext contains all files
- [ ] Counts are accurate
- [ ] filesOfType() filters correctly
- [ ] Tests pass" \
    "type:task,phase:2-scanner,pkg:scanner,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Create scanner test fixtures" \
    "## Description
Create fixture directories for testing:
- \`tests/Fixtures/laravel-app/\` — minimal Laravel 11 structure with all standard dirs
- \`tests/Fixtures/empty-project/\` — empty directory
- \`tests/Fixtures/non-laravel/\` — generic PHP project without Laravel
- \`tests/Fixtures/custom-structure/\` — non-standard paths

## Acceptance Criteria
- [ ] All fixtures exist
- [ ] Fixtures are minimal but representative
- [ ] Used by scanner tests" \
    "type:test,phase:2-scanner,pkg:scanner,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write scanner tests" \
    "## Description
Full test suite covering:
- Valid Laravel project discovery
- Missing directories handled gracefully
- Custom path config respected
- Exclusion patterns work
- File classification correct
- Framework detection
- Empty project
- Non-Laravel project
- Large project performance

## Acceptance Criteria
- [ ] 90%+ coverage
- [ ] All edge cases tested
- [ ] Tests use fixture directories" \
    "type:test,phase:2-scanner,pkg:scanner,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write scanner benchmarks" \
    "## Description
Benchmark files/second and memory usage:
- 100 files
- 500 files
- 1000 files
- 5000 files (generated fixture)

## Acceptance Criteria
- [ ] Benchmarks run with clear metrics
- [ ] Memory stays bounded (lazy iteration)" \
    "type:perf,phase:2-scanner,pkg:scanner,P2-medium" \
    "v0.1.0 — First Light"

# =============================================================================
# PHASE 3 — ROUTE ANALYZER + JSON EXPORTER
# =============================================================================

echo ""
echo "=== Phase 3 — Route Analyzer ==="
echo ""

create_issue \
    "[Epic] Phase 3 — Route Analyzer" \
    "## Overview
First working analyzer. Extract all route information from Laravel route files.

## Depends On
- Core package with Parser (Phase 1)
- Scanner (Phase 2)

## Acceptance Criteria
- [ ] All route types handled (basic, resource, API resource, closure, controller)
- [ ] Route groups resolve correctly (prefix, middleware, namespace)
- [ ] Nodes and edges generated per JSON_SCHEMA.md
- [ ] Malformed files don't crash
- [ ] 90%+ coverage
- [ ] Benchmarked" \
    "type:epic,phase:3-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement RouteAnalyzer class structure" \
    "## Description
Create \`CodeAtlas\Analyzers\Routes\RouteAnalyzer\` implementing \`AnalyzerInterface\`.

Structure:
- \`RouteAnalyzer\` — main class, delegates to extractors
- \`DTOs/RouteData\` — readonly DTO for a single route
- \`DTOs/RouteCollection\` — collection of RouteData
- \`Extractors/BasicRouteExtractor\` — GET/POST/PUT/DELETE
- \`Extractors/ResourceRouteExtractor\` — resource/apiResource
- \`Extractors/GroupExtractor\` — route groups
- \`Exceptions/RouteAnalyzerException\`

## Acceptance Criteria
- [ ] Class structure created
- [ ] Implements AnalyzerInterface
- [ ] Delegates to extractors
- [ ] PHPStan passes" \
    "type:task,phase:3-routes,pkg:analyzer-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Extract basic routes (GET/POST/PUT/PATCH/DELETE/OPTIONS)" \
    "## Description
Parse \`Route::get()\`, \`Route::post()\`, etc. from AST.

Extract: URI, HTTP method, closure vs controller.

Handle:
- String action: \`Route::get('/users', 'UserController@index')\`
- Array action: \`Route::get('/users', [UserController::class, 'index'])\`
- Closure action: \`Route::get('/health', function() {...})\`
- Invokable: \`Route::get('/users', UserController::class)\`

## Acceptance Criteria
- [ ] All HTTP methods detected
- [ ] All action styles handled
- [ ] URI extracted correctly
- [ ] Tests with fixtures for each style" \
    "type:task,phase:3-routes,pkg:analyzer-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Extract resource and API resource routes" \
    "## Description
Parse \`Route::resource()\` and \`Route::apiResource()\`.

Expand into individual routes:
- resource: index, create, store, show, edit, update, destroy
- apiResource: index, store, show, update, destroy

Handle \`->only()\` and \`->except()\` modifiers.

## Acceptance Criteria
- [ ] Resource routes expand correctly (7 routes)
- [ ] API resource routes expand correctly (5 routes)
- [ ] only() and except() filter correctly
- [ ] Tests pass" \
    "type:task,phase:3-routes,pkg:analyzer-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Extract route groups with prefix, middleware, namespace" \
    "## Description
Parse \`Route::group()\`, \`Route::prefix()\`, \`Route::middleware()\`, \`Route::name()\` group wrappers.

Resolve nested groups — attributes accumulate:
- Prefixes concatenate: \`/api\` + \`/v1\` = \`/api/v1\`
- Middleware merges: \`['api']\` + \`['auth']\` = \`['api', 'auth']\`
- Names concatenate: \`'api.'\` + \`'users.'\`

## Acceptance Criteria
- [ ] Simple groups work
- [ ] Nested groups resolve correctly
- [ ] Prefix, middleware, name, domain all propagate
- [ ] Tests with deeply nested fixtures" \
    "type:task,phase:3-routes,pkg:analyzer-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Extract route metadata (name, domain, where, parameters)" \
    "## Description
Parse chained methods:
- \`->name('users.index')\`
- \`->domain('api.example.com')\`
- \`->where('id', '[0-9]+')\`
- \`->whereNumber('id')\`
- \`->whereAlpha('slug')\`

Extract URI parameters: \`{id}\`, \`{user?}\` (optional).

## Acceptance Criteria
- [ ] Names captured
- [ ] Domain captured
- [ ] Where constraints captured
- [ ] Parameters extracted from URI
- [ ] Optional parameters detected
- [ ] Tests pass" \
    "type:task,phase:3-routes,pkg:analyzer-routes,P2-medium" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Generate Route nodes and edges" \
    "## Description
Convert extracted RouteData into:
- \`Node\` objects with \`type: NodeType::Route\`
- \`Edge\` objects:
  - Route → Controller (\`EdgeType::RoutesTo\`)
  - Route → Middleware (\`EdgeType::UsesMiddleware\`)

Node ID format: \`route::get::/api/users\`

## Acceptance Criteria
- [ ] Nodes conform to JSON_SCHEMA.md
- [ ] Edges link to correct targets
- [ ] IDs are deterministic and unique
- [ ] Tests pass" \
    "type:task,phase:3-routes,pkg:analyzer-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Handle malformed route files gracefully" \
    "## Description
- Catch \`ParserException\` on unparseable files
- Log warning with filename and error
- Skip file, continue with remaining
- Include error in \`AnalysisResult.errors\`
- Never crash the full analysis

## Acceptance Criteria
- [ ] Malformed file doesn't crash
- [ ] Error is logged and included in results
- [ ] Remaining files still analyzed
- [ ] Tests with intentionally broken fixtures" \
    "type:task,phase:3-routes,pkg:analyzer-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Create route analyzer test fixtures" \
    "## Description
Create fixture route files in \`tests/Fixtures/routes/\`:
- \`web.php\` — mixed basic routes
- \`api.php\` — API routes with groups and middleware
- \`resource-routes.php\` — resource and apiResource
- \`nested-groups.php\` — deeply nested groups
- \`closure-routes.php\` — closure-only routes
- \`complex.php\` — all features combined
- \`malformed.php\` — intentionally broken syntax
- \`empty.php\` — empty file

## Acceptance Criteria
- [ ] Fixtures cover all route types
- [ ] Malformed fixture exists for error handling tests" \
    "type:test,phase:3-routes,pkg:analyzer-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write route analyzer unit + integration tests" \
    "## Description
Unit tests: test each extractor independently.
Integration tests: full flow fixture → scanner → analyzer → JSON.

## Acceptance Criteria
- [ ] Each extractor tested independently
- [ ] Integration test validates full JSON output against schema
- [ ] Error handling tested
- [ ] 90%+ coverage" \
    "type:test,phase:3-routes,pkg:analyzer-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write route analyzer benchmarks" \
    "## Description
Benchmark routes/second with 10, 50, 100, 500 route definitions.

## Acceptance Criteria
- [ ] Benchmarks produce clear metrics" \
    "type:perf,phase:3-routes,pkg:analyzer-routes,P2-medium" \
    "v0.1.0 — First Light"

# JSON Exporter

create_issue \
    "[Epic] Phase 3 — JSON Exporter" \
    "## Overview
Export AnalysisResult as JSON conforming to JSON_SCHEMA.md.

## Acceptance Criteria
- [ ] Output matches schema exactly
- [ ] Schema version stamped
- [ ] Pretty-print option
- [ ] Full test coverage" \
    "type:epic,phase:3-routes,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement JsonExporter class" \
    "## Description
Create \`CodeAtlas\Exporters\Json\JsonExporter\` implementing \`ExporterInterface\`.

Accept \`AnalysisResult\`. Output JSON conforming to top-level schema in JSON_SCHEMA.md:
- \$schema URL
- version
- project metadata
- analysis metadata
- graph (nodes + edges)
- results (per-analyzer)
- errors

## Acceptance Criteria
- [ ] JSON output validates against schema
- [ ] Schema version included
- [ ] Project metadata included
- [ ] All analyzer results included
- [ ] Errors included
- [ ] Tests pass" \
    "type:task,phase:3-routes,pkg:exporter-json,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write JSON exporter tests" \
    "## Description
- Empty results → valid JSON
- Single analyzer → correct structure
- Multiple analyzers → merged correctly
- Large results → valid JSON
- Pretty-print toggle

## Acceptance Criteria
- [ ] All scenarios tested
- [ ] Output structure validated
- [ ] Tests pass" \
    "type:test,phase:3-routes,pkg:exporter-json,P1-high" \
    "v0.1.0 — First Light"

# =============================================================================
# PHASE 4 — LARAVEL BRIDGE + UI MVP
# =============================================================================

echo ""
echo "=== Phase 4 — Laravel Bridge + UI MVP ==="
echo ""

create_issue \
    "[Epic] Phase 4 — Laravel Bridge" \
    "## Overview
Laravel integration: ServiceProvider, artisan commands, config publishing, web UI routes.

## Acceptance Criteria
- [ ] \`php artisan codeatlas:scan\` works
- [ ] \`php artisan codeatlas:analyze\` works
- [ ] \`php artisan codeatlas:export\` works
- [ ] Config publishable
- [ ] Web UI accessible at configured route
- [ ] Orchestra Testbench tests pass" \
    "type:epic,phase:4-mvp,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement CodeAtlasServiceProvider" \
    "## Description
Register all bindings: Scanner, Parser, Analyzers, Exporters, Config.
Merge config. Register routes. Publish config.

## Acceptance Criteria
- [ ] Provider boots in Laravel app
- [ ] All services resolvable
- [ ] Config merges correctly
- [ ] Routes registered" \
    "type:task,phase:4-mvp,pkg:laravel,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement codeatlas:scan artisan command" \
    "## Description
Artisan command that runs scanner on current project.
Options: \`--path\` (default: base_path())
Output: summary table showing discovered files by type.

## Acceptance Criteria
- [ ] Command runs and shows results
- [ ] --path option works
- [ ] Output is readable" \
    "type:task,phase:4-mvp,pkg:laravel,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement codeatlas:analyze artisan command" \
    "## Description
Run full pipeline. Options: \`--analyzer\` (filter), \`--output\` (path).
Save JSON to \`storage/codeatlas/analysis.json\`.

## Acceptance Criteria
- [ ] Full pipeline runs
- [ ] JSON saved to disk
- [ ] --analyzer filter works
- [ ] Summary output shown" \
    "type:task,phase:4-mvp,pkg:laravel,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Create publishable config and web routes" \
    "## Description
\`config/codeatlas.php\`: scan_paths, exclude_paths, analyzers (enable/disable), export_path, ui_enabled, ui_route_prefix, ui_middleware.

Routes: \`GET /codeatlas\` (SPA), \`GET /codeatlas/api/analysis\` (JSON endpoint).

## Acceptance Criteria
- [ ] \`php artisan vendor:publish --tag=codeatlas-config\` works
- [ ] Routes serve correct content
- [ ] Middleware configurable" \
    "type:task,phase:4-mvp,pkg:laravel,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write Laravel bridge tests (Orchestra Testbench)" \
    "## Description
Test provider registration, commands, routes, config using Orchestra Testbench.

## Acceptance Criteria
- [ ] Provider test passes
- [ ] Command tests pass
- [ ] Route tests pass
- [ ] Config test passes" \
    "type:test,phase:4-mvp,pkg:laravel,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Epic] Phase 4 — Basic Web UI (MVP)" \
    "## Overview
Minimal interactive UI showing routes on a graph. First visual output of CodeAtlas.

## Acceptance Criteria
- [ ] App shell with sidebar, canvas, inspector
- [ ] Routes displayed as nodes on React Flow canvas
- [ ] Click node shows properties
- [ ] Search/filter works
- [ ] Dark/light theme" \
    "type:epic,phase:4-mvp,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Build app shell layout" \
    "## Description
Three-panel layout per UI_GUIDELINES.md: sidebar (240px), canvas (flex), inspector (320px, collapsible). Bottom console panel (collapsible). All panels resizable via drag handles. Dark theme default.

## Acceptance Criteria
- [ ] Layout matches spec
- [ ] Panels resize and collapse
- [ ] Dark theme applied
- [ ] Responsive behavior on narrow screens" \
    "type:task,phase:4-mvp,pkg:ui,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement data loading with TanStack Query" \
    "## Description
Create \`useAnalysis()\` hook. Fetch JSON from API endpoint. Type the response as \`AnalysisResult\`. Handle loading, error, empty states.

## Acceptance Criteria
- [ ] Data fetches and is typed
- [ ] Loading state shown
- [ ] Error state shown
- [ ] Empty state handled" \
    "type:task,phase:4-mvp,pkg:ui,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Create RouteNode component for React Flow" \
    "## Description
Custom React Flow node for routes. Blue coloring (#3b82f6) per UI_GUIDELINES.md. Show: colored dot, label (METHOD /uri), name if present.

## Acceptance Criteria
- [ ] Node renders with correct styling
- [ ] Method and URI visible
- [ ] Matches design spec" \
    "type:task,phase:4-mvp,pkg:ui,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Render route graph on canvas" \
    "## Description
Convert analysis nodes to React Flow nodes. Apply automatic layout (dagre or elkjs). Render edges between routes and controllers/middleware.

## Acceptance Criteria
- [ ] All routes appear as nodes
- [ ] Edges connect to controllers
- [ ] Layout is readable (no overlapping)
- [ ] Zoom, pan, minimap work" \
    "type:task,phase:4-mvp,pkg:ui,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement sidebar with route list" \
    "## Description
Routes section in sidebar. Show all routes with HTTP method badge and URI. Click to select on canvas. Count badge on section header.

## Acceptance Criteria
- [ ] Route list renders
- [ ] Click selects node on canvas
- [ ] Count badge accurate" \
    "type:task,phase:4-mvp,pkg:ui,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement inspector panel" \
    "## Description
Click a node → show properties in right panel. Show: type, label, file path, line range, all metadata fields. Show connections (incoming/outgoing edges).

## Acceptance Criteria
- [ ] Inspector shows correct data for selected node
- [ ] Connections listed
- [ ] File path shown" \
    "type:task,phase:4-mvp,pkg:ui,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement Cmd+K search" \
    "## Description
Global search triggered by Cmd+K. Search nodes by name, type, URI. Highlight matching nodes. Select from results.

## Acceptance Criteria
- [ ] Cmd+K opens search
- [ ] Results filter as you type
- [ ] Selecting result centers graph on node
- [ ] Esc closes search" \
    "type:task,phase:4-mvp,pkg:ui,P2-medium" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement dark/light theme toggle" \
    "## Description
Toggle between CSS variable sets from UI_GUIDELINES.md. Persist in localStorage. Default to dark.

## Acceptance Criteria
- [ ] Both themes render correctly
- [ ] Preference persists across sessions
- [ ] Toggle accessible in toolbar" \
    "type:task,phase:4-mvp,pkg:ui,P2-medium" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write frontend tests" \
    "## Description
Vitest + React Testing Library:
- App renders without crash
- Sidebar shows routes from fixture data
- Canvas renders nodes
- Click node opens inspector
- Search filters nodes

## Acceptance Criteria
- [ ] Smoke tests pass
- [ ] Key interactions tested" \
    "type:test,phase:4-mvp,pkg:ui,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] v0.1.0 release preparation" \
    "## Description
- End-to-end integration test: install package → scan → analyze → view
- README with installation instructions, screenshots, GIFs
- CHANGELOG entry
- Packagist submission preparation
- Tag v0.1.0

## Acceptance Criteria
- [ ] E2E test passes
- [ ] README is comprehensive
- [ ] Package installable via Composer" \
    "type:chore,phase:4-mvp,P1-high" \
    "v0.1.0 — First Light"

# =============================================================================
# PHASES 5-11 — EPICS ONLY (tasks created when sprint starts)
# =============================================================================

echo ""
echo "=== Phases 5-11 — Epics ==="
echo ""

create_issue \
    "[Epic] Phase 5 — Middleware Analyzer" \
    "Extract middleware classes, aliases, groups, priority, and route usage mapping.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:5-controllers,P2-medium" \
    "v0.2.0 — Connections"

create_issue \
    "[Epic] Phase 5 — Controller Analyzer" \
    "Extract controller classes with methods, constructor dependencies, traits, return types, PHP 8 attributes.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:5-controllers,P1-high" \
    "v0.2.0 — Connections"

create_issue \
    "[Epic] Phase 6 — Service Analyzer" \
    "Detect service classes by convention and binding. Map constructor dependency chains.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:6-services,P2-medium" \
    "v0.3.0 — Depth"

create_issue \
    "[Epic] Phase 6 — Repository Analyzer" \
    "Map repository classes to their Eloquent models.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:6-services,P2-medium" \
    "v0.3.0 — Depth"

create_issue \
    "[Epic] Phase 7 — Model Analyzer" \
    "Extract Eloquent model metadata: relationships (all types), scopes, casts, fillable, observers, events, factories.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:7-models,P1-high" \
    "v0.4.0 — Relationships"

create_issue \
    "[Epic] Phase 7 — Database/Migration Analyzer" \
    "Parse migration files. Extract tables, columns, indexes, foreign keys. Generate ER diagram data.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:7-models,P2-medium" \
    "v0.4.0 — Relationships"

create_issue \
    "[Epic] Phase 8 — Event Analyzer" \
    "Map event dispatching and listening flow. Detect Event→Listener bindings and dispatch locations.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:8-events,P2-medium" \
    "v0.5.0 — Flow"

create_issue \
    "[Epic] Phase 8 — Job Analyzer" \
    "Analyze queue jobs with full configuration: queue, connection, tries, timeout, chains, batches.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:8-events,P2-medium" \
    "v0.5.0 — Flow"

create_issue \
    "[Epic] Phase 8 — Notification Analyzer" \
    "Map notification classes with channels (mail, database, broadcast, SMS) and recipients.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:8-events,P2-medium" \
    "v0.5.0 — Flow"

create_issue \
    "[Epic] Phase 9 — Policy Analyzer" \
    "Map policies to models. Extract authorization methods and parameters.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:9-policies,P2-medium" \
    "v0.6.0 — Authority"

create_issue \
    "[Epic] Phase 9 — Schedule Analyzer" \
    "Extract scheduled commands from Console Kernel with frequency, timezone, constraints.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:9-policies,P2-medium" \
    "v0.6.0 — Authority"

create_issue \
    "[Epic] Phase 9 — Cache Analyzer" \
    "Detect cache usage patterns: keys, TTLs, drivers, tags.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:9-policies,P3-low" \
    "v0.6.0 — Authority"

create_issue \
    "[Epic] Phase 10 — Full Dependency Graph" \
    "Cross-analyzer dependency resolution. Full architecture view. Circular dependency detection.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:10-deps,P1-high" \
    "v0.7.0 — Big Picture"

create_issue \
    "[Epic] Phase 10 — Architecture Dashboard" \
    "Overview page: totals per type, health metrics, top-level architecture diagram, code statistics.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:10-deps,P2-medium" \
    "v0.7.0 — Big Picture"

create_issue \
    "[Epic] Phase 11 — Tauri Desktop App" \
    "Native desktop application. File picker, menu bar, auto-updates, system tray.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,phase:11-desktop,P2-medium" \
    "v0.8.0 — Desktop"

create_issue \
    "[Epic] VS Code Extension" \
    "Webview panel with React Flow graph. 'Open in CodeAtlas' command. File↔node navigation.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,P2-medium" \
    "v0.9.0 — Editor"

create_issue \
    "[Epic] v1.0.0 — Stable Release" \
    "API freeze, full documentation site, performance optimization pass, security audit, plugin development guide.

Tasks will be created when this epic enters sprint planning." \
    "type:epic,P1-high" \
    "v1.0.0 — Atlas"

echo ""
echo "======================================="
echo "  Issue creation complete!"
echo "======================================="
echo ""
echo "Summary:"
echo "  Phase 0 (infra):      ~20 issues  [Part 1 script]"
echo "  Phase 1 (contracts):  ~15 issues  [Part 1 script]"
echo "  Phase 1 (core):       ~12 issues  [This script]"
echo "  Phase 2 (scanner):    ~10 issues  [This script]"
echo "  Phase 3 (routes):     ~12 issues  [This script]"
echo "  Phase 4 (bridge+UI):  ~16 issues  [This script]"
echo "  Phases 5-11 (epics):  ~17 epics   [This script]"
echo "  ─────────────────────────────────"
echo "  Total:               ~102 issues"
echo ""
echo "Phases 5-11 tasks (~100-150 more) will be created"
echo "when each phase enters sprint planning."
echo ""
echo "Total projected backlog: 200-250 issues"
