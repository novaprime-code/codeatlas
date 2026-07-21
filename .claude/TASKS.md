# CodeAtlas — Current Tasks

## Completed

### Sprint 0.1 — Repository Skeleton ✅
### Sprint 1.1 — Contracts Package ✅ (34 assertions)
### Sprint 1.2 — Core Engine ✅ (114 assertions)

### Sprint 2.1 — Scanner Package ✅
- ✅ FileClassifier (pure prefix → FileType with custom overrides)
- ✅ ComposerReader + ComposerMetadata (name, php, deps, PSR-4)
- ✅ FrameworkDetector (Laravel via artisan + composer dependency)
- ✅ DirectoryWalker (Symfony Finder, lazy iteration, glob + directory exclusions)
- ✅ Scanner (composition, path validation, ProjectContext assembly)
- ✅ Fixture Laravel 11 project (21 files across every standard directory)
- ✅ Fixture empty and non-Laravel projects
- ✅ **Runtime verification: 59/59 assertions**
- ✅ Real bug caught: default ScanConfig missed `resources/` — fixed per ARCHITECTURE.md

**Cumulative runtime verification: 207 assertions passing across contracts + core + scanner.**

---

## Active: Sprint 3.1 — Route Analyzer

### Task 3.1.1 — RouteAnalyzer class structure ⬜
Implement `CodeAtlas\Analyzers\Routes\RouteAnalyzer implements AnalyzerInterface`.

### Task 3.1.2 — Basic route extraction ⬜
Parse `Route::get()`, `Route::post()`, etc. Handle string, array, closure, and invokable action styles.

### Task 3.1.3 — Resource routes ⬜
Expand `Route::resource()` and `Route::apiResource()` with `only()` and `except()` filters.

### Task 3.1.4 — Route groups ⬜
Resolve nested groups with prefix, middleware, name concatenation.

### Task 3.1.5 — Route metadata ⬜
`->name()`, `->domain()`, `->where()`, URI parameters.

### Task 3.1.6 — Node and edge generation ⬜
Convert to `Node` (type: Route) and `Edge` (RoutesTo, UsesMiddleware) records.

### Task 3.1.7 — Malformed file handling ⬜
Log warning, skip file, continue with remaining; error captured in `AnalysisResult.errors`.

### Task 3.1.8 — Route fixtures + Pest tests ⬜
Fixtures for basic, resource, closure, nested groups, malformed. Integration test round-trips through the pipeline.

---

## Backlog (Sprint 3.2 — JSON Exporter)

- JsonExporter implementing ExporterInterface
- Full JSON_SCHEMA.md conformance
- Schema version stamping
- Round-trip test against a real analyzer result
