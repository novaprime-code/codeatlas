# CodeAtlas — Current Tasks

## Completed

### Sprint 0.1 — Repository Skeleton ✅
### Sprint 1.1 — Contracts Package ✅ (34 assertions)
### Sprint 1.2 — Core Engine ✅ (114 assertions)
### Sprint 2.1 — Scanner Package ✅ (59 assertions)
### Sprint 3.1 — Route Analyzer ✅ (79 assertions)

### Sprint 3.2 — JSON Exporter ✅
- ✅ JsonExporter implementing ExporterInterface (name: 'json')
- ✅ Full JSON_SCHEMA.md document: $schema, version, project, analysis, graph, results, errors
- ✅ Schema version stamping (1.0.0) on every export
- ✅ prettyPrint honoured from ExportConfig
- ✅ Project metadata via ExportConfig options (bridge will supply from ProjectContext)
- ✅ Merged pipeline results pass through; single results wrapped under analyzer name
- ✅ Empty-map normalization: {} not [] (TypeScript Record compatibility)
- ✅ JsonExporterPlugin for container registration
- ✅ **Runtime verification: 57/57 assertions** including full round-trip
    (real Scanner → Parser → RouteAnalyzer → JsonExporter → json_decode → schema checks)
- ✅ Real interop bug caught by eyeballing output: PHP empty maps serialized as []
    breaking TS Record<string, T> — normalized in the exporter

**Cumulative runtime verification: 343 assertions across 5 packages.**

**MILESTONE: THE BACKEND PIPELINE IS COMPLETE.**

```
Source → Scanner ✅ → AST Parser ✅ → Analyzer ✅ → DTO ✅ → JSON ✅ → UI (next)
```

A real Laravel project now goes in one end and a schema-conformant JSON
document comes out the other. Everything the constitution calls "backend"
exists and is verified.

---

## Active: Sprint 4.1 — Laravel Bridge

### Task 4.1.1 — CodeAtlasServiceProvider ⬜
Register container, parser, scanner, plugins (routes analyzer + json exporter); merge publishable config.

### Task 4.1.2 — codeatlas:analyze command ⬜
Run the pipeline against the app base path; write JSON to storage or stdout; --analyzer filter, --pretty flag.

### Task 4.1.3 — codeatlas:scan command ⬜
Scan-only: print discovered file counts by type.

### Task 4.1.4 — codeatlas:export command ⬜
Re-export the last analysis with a different format/config.

### Task 4.1.5 — config/codeatlas.php ⬜
Publishable config: scan paths, exclusions, analyzer toggles, output path.

### Task 4.1.6 — Orchestra Testbench integration tests ⬜
Boot the provider in a Testbench app, run codeatlas:analyze, assert JSON output.

---

## Backlog (Sprint 4.2 — Web UI MVP)

- Load analysis JSON via TanStack Query
- Route list in sidebar (types already mirrored in types/analysis.ts)
- React Flow canvas with Route nodes
- Inspector panel with node metadata
- First visual output — v0.1.0 release
