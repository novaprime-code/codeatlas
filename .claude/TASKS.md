# CodeAtlas — Current Tasks

## Completed

### Sprint 0.1 — Repository Skeleton ✅
### Sprint 1.1 — Contracts Package ✅ (34 assertions)
### Sprint 1.2 — Core Engine ✅ (114 assertions)
### Sprint 2.1 — Scanner Package ✅ (59 assertions)

### Sprint 3.1 — Route Analyzer ✅
- ✅ RouteData, RouteCollection, GroupContext DTOs
- ✅ ChainUnwinder (flattens fluent Route chains → ordered operations)
- ✅ ValueResolver (string/array/class-const literal extraction)
- ✅ ActionResolver (controller/invokable/closure/string@method)
- ✅ RouteExtractor (context-aware descent, nested group stack)
- ✅ RouteAnalyzer (implements AnalyzerInterface, fault isolation)
- ✅ RouteAnalyzerPlugin (container registration)
- ✅ Fixtures: web, api, resource, malformed, empty + real integration-app
- ✅ **Extraction verification: 40/40 assertions**
- ✅ **Integration verification: 39/39 assertions** (real Scanner + Parser + Analyzer)
- ✅ Handles: verbs, any, match, resource, apiResource, closures, invokables,
      fluent + array groups, nested groups, middleware accumulation, where
      shortcuts, URI parameters, name prefixes

**Cumulative runtime verification: 286 assertions passing across contracts + core + scanner + analyzer-routes.**

**MILESTONE: CodeAtlas produces its first architecture graph from a real Laravel project.**

The sacred pipeline is now 4/6 complete:
Source → Scanner ✅ → AST Parser ✅ → Analyzer ✅ → DTO ✅ → JSON (next) → UI

---

## Active: Sprint 3.2 — JSON Exporter

### Task 3.2.1 — JsonExporter class ⬜
Implement `CodeAtlas\Exporters\Json\JsonExporter implements ExporterInterface`.
Produce the top-level document per JSON_SCHEMA.md: $schema, version, project, analysis, graph, results, errors.

### Task 3.2.2 — Schema version stamping ⬜
Include `$schema` URL and `version` in every output.

### Task 3.2.3 — Pretty-print option ⬜
Honour `ExportConfig::$prettyPrint`.

### Task 3.2.4 — Round-trip test ⬜
Run RouteAnalyzer → JsonExporter, parse the JSON back, verify structure matches schema.

---

## Backlog (Sprint 4.1 — Laravel Bridge + UI MVP)

- CodeAtlasServiceProvider
- codeatlas:scan / codeatlas:analyze / codeatlas:export artisan commands
- Publishable config + web routes
- React UI: app shell, route list, React Flow canvas, inspector
- First visual output — v0.1.0 release
