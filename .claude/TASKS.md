# CodeAtlas — Current Tasks

## Completed

### Sprint 0.1 — Repository Skeleton ✅
### Sprint 1.1 — Contracts ✅ (34 assertions)
### Sprint 1.2 — Core Engine ✅ (114 assertions)
### Sprint 2.1 — Scanner ✅ (59 assertions)
### Sprint 3.1 — Route Analyzer ✅ (79 assertions)
### Sprint 3.2 — JSON Exporter ✅ (57 assertions)

### Sprint 4.1 — Laravel Bridge ✅
- ✅ CodeAtlasFactory — framework-free composition root (container + parser + scanner + plugins + PipelineRunner)
- ✅ AnalysisWriter — framework-free disk writer
- ✅ CodeAtlasServiceProvider (config merge, publish, command registration)
- ✅ codeatlas:analyze (--analyzer filter, --output override, --compact)
- ✅ codeatlas:scan (discovery dry run)
- ✅ config/codeatlas.php publishable config
- ✅ PipelineRunner now auto-injects project metadata + duration into ExportConfig (core change)
- ✅ Orchestra Testbench integration tests (run locally: composer require --dev orchestra/testbench)
- ✅ **Runtime verification: 45/45** —
    25 factory/pipeline/writer assertions incl. FIRST FULL REAL-COMPONENT PIPELINE RUN,
    20 Laravel-shell assertions against REAL Laravel 11 source
    (commands constructed by Illuminate\Console\Parser — signatures proven valid)
- ✅ **Real container bug caught & fixed**: optional class-typed constructor params
    (e.g. `?Parser $parser = null`) threw instead of falling back to default

**Cumulative runtime verification: 408 assertions across 6 packages.**

**The entire PHP backend is now installable in a real Laravel app:**
`composer require codeatlas/laravel && php artisan codeatlas:analyze`

---

## Active: Sprint 4.2 — Web UI MVP

### Task 4.2.1 — JSON loading ⬜
TanStack Query hook loading the analysis document (file upload + URL fetch).

### Task 4.2.2 — Sidebar route list ⬜
Grouped, counted, searchable list from graph.nodes.

### Task 4.2.3 — React Flow canvas ⬜
Route nodes rendered with UI_GUIDELINES.md colors; RoutesTo/UsesMiddleware edges.

### Task 4.2.4 — Inspector panel ⬜
Selected node metadata (URI, methods, controller, middleware, constraints).

### Task 4.2.5 — Smoke + component tests ⬜
Vitest + React Testing Library against a fixture analysis document.

Then: v0.1.0 release checklist.
