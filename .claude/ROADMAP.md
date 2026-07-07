# CodeAtlas Roadmap

## Release Plan

| Version | Codename | Contents | Status |
|---------|----------|----------|--------|
| v0.0.1 | Foundation | Infrastructure, CI, tooling | 🔜 Current |
| v0.1.0 | First Light | Core, Contracts, Scanner, Route Analyzer, JSON Exporter, Basic Viewer | ⬜ |
| v0.2.0 | Connections | Middleware Analyzer, Controller Analyzer | ⬜ |
| v0.3.0 | Depth | Service Analyzer, Repository Analyzer | ⬜ |
| v0.4.0 | Relationships | Model Analyzer, Database/Migration Analyzer | ⬜ |
| v0.5.0 | Flow | Event Analyzer, Job Analyzer, Notification Analyzer | ⬜ |
| v0.6.0 | Authority | Policy Analyzer, Schedule Analyzer, Cache Analyzer | ⬜ |
| v0.7.0 | Big Picture | Dependency Graph (full), Architecture Dashboard | ⬜ |
| v0.8.0 | Desktop | Tauri desktop app | ⬜ |
| v0.9.0 | Editor | VS Code extension | ⬜ |
| v1.0.0 | Atlas | Stable release, public API freeze | ⬜ |

---

## Phase 0 — Infrastructure (v0.0.1)

**Goal:** Repository is set up, CI passes, all tooling configured. Zero features. Zero business logic.

### PHP Infrastructure
- [ ] Initialize root `composer.json` with monorepo configuration
- [ ] Create `packages/contracts/composer.json` with PSR autoloading
- [ ] Create `packages/core/composer.json`
- [ ] Create `packages/scanner/composer.json`
- [ ] Create `packages/laravel/composer.json`
- [ ] Create `packages/analyzers/routes/composer.json`
- [ ] Create `packages/exporters/json/composer.json`
- [ ] Configure Pest across all packages
- [ ] Configure PHPStan (level max) across all packages
- [ ] Configure Laravel Pint (PER preset) across all packages
- [ ] Configure Rector for automated refactoring rules
- [ ] Create root phpstan.neon.dist with baseline
- [ ] Create root pint.json
- [ ] Create root rector.php
- [ ] Write a single smoke test per package that passes

### Frontend Infrastructure
- [ ] Initialize `apps/web/` with Vite + React + TypeScript
- [ ] Configure Tailwind CSS
- [ ] Install and configure shadcn/ui
- [ ] Install React Flow
- [ ] Install Monaco Editor
- [ ] Install TanStack Query
- [ ] Install Zustand
- [ ] Configure ESLint (strict TypeScript rules)
- [ ] Configure Prettier
- [ ] Write a single smoke render test

### Monorepo Infrastructure
- [ ] Initialize PNPM workspaces for frontend packages
- [ ] Configure Turborepo for build orchestration
- [ ] Create root `Makefile` with common commands
- [ ] Create root `docker-compose.yml` for development (optional)

### CI/CD
- [ ] GitHub Actions: PHP lint (Pint)
- [ ] GitHub Actions: PHP static analysis (PHPStan)
- [ ] GitHub Actions: PHP tests (Pest) — matrix across PHP 8.3, 8.4
- [ ] GitHub Actions: Frontend lint (ESLint)
- [ ] GitHub Actions: Frontend tests (Vitest)
- [ ] GitHub Actions: Frontend build check
- [ ] Setup Commitlint for conventional commits
- [ ] Setup Husky for pre-commit hooks (lint-staged)
- [ ] Setup Release Please for automated releases

### Documentation
- [ ] Create root README.md with project overview
- [ ] Create CONTRIBUTING.md
- [ ] Create LICENSE (MIT)
- [ ] Create CHANGELOG.md
- [ ] Create `.github/ISSUE_TEMPLATE/` (bug, feature, task)
- [ ] Create `.github/PULL_REQUEST_TEMPLATE.md`

### Deliverable
A repository where `make test`, `make lint`, `make analyze` all pass with zero features. Every package has a skeleton with passing CI.

---

## Phase 1 — Core + Contracts (v0.1.0 part 1)

**Goal:** The foundation packages exist with real implementations. No analyzers yet.

### Contracts Package
- [ ] `AnalyzerInterface`
- [ ] `ScannerInterface`
- [ ] `ExporterInterface`
- [ ] `ParserInterface`
- [ ] `NodeInterface`
- [ ] `EdgeInterface`
- [ ] `GraphInterface`
- [ ] `PluginInterface`
- [ ] `ConfigInterface`
- [ ] `ProjectContext` value object
- [ ] `AnalysisResult` value object
- [ ] `FileReference` value object
- [ ] `NodeType` enum
- [ ] `EdgeType` enum
- [ ] `FileType` enum
- [ ] `Severity` enum
- [ ] Exception base classes
- [ ] Full test coverage
- [ ] Package documentation

### Core Package
- [ ] Container (minimal DI)
- [ ] Configuration loader (array + file)
- [ ] Plugin loader (auto-discovers analyzers/exporters)
- [ ] Event bus (sync, in-process)
- [ ] Logger (PSR-3 wrapper)
- [ ] Pipeline runner (scan → analyze → export orchestration)
- [ ] Parser wrapper around `nikic/php-parser`
- [ ] DTO factory
- [ ] Exception system
- [ ] Full test coverage
- [ ] Benchmarks
- [ ] Package documentation

---

## Phase 2 — Scanner (v0.1.0 part 2)

**Goal:** Given a path, discover all analyzable files in a Laravel project.

- [ ] Implement `ScannerInterface`
- [ ] Directory walker using Symfony Finder
- [ ] Configurable scan paths
- [ ] Configurable exclusion patterns
- [ ] `ProjectContext` builder
- [ ] Framework detection (is this a Laravel project?)
- [ ] `composer.json` parser for metadata
- [ ] File classification (route file, controller, model, etc.)
- [ ] Full test coverage with fixture Laravel projects
- [ ] Benchmarks (files/second on 1000+ file projects)
- [ ] Package documentation

---

## Phase 3 — Route Analyzer + JSON Exporter (v0.1.0 part 3)

**Goal:** First working analyzer. Scan a Laravel project, extract routes, export JSON.

### Route Analyzer
- [ ] Implement `AnalyzerInterface`
- [ ] Parse `routes/web.php`, `routes/api.php`, `routes/channels.php`, `routes/console.php`
- [ ] Extract: URI, methods, name, controller, action, middleware, prefix, domain, where constraints
- [ ] Handle closure routes
- [ ] Handle controller routes
- [ ] Handle resource routes
- [ ] Handle API resource routes
- [ ] Handle route groups
- [ ] Handle route prefixes
- [ ] Handle route middleware
- [ ] Handle route model binding
- [ ] Generate Route nodes
- [ ] Generate Route → Controller edges
- [ ] Generate Route → Middleware edges
- [ ] Full test coverage
- [ ] Benchmarks
- [ ] Documentation

### JSON Exporter
- [ ] Implement `ExporterInterface`
- [ ] Produce JSON conforming to `JSON_SCHEMA.md`
- [ ] Schema version stamping
- [ ] Pretty-print option
- [ ] Streaming output for large results
- [ ] Full test coverage
- [ ] Documentation

---

## Phase 4 — Laravel Bridge + Basic Web UI (v0.1.0 part 4)

**Goal:** A usable MVP. Install a Composer package, run `php artisan codeatlas:analyze`, see routes in a browser.

### Laravel Bridge
- [ ] `CodeAtlasServiceProvider`
- [ ] `codeatlas:scan` command
- [ ] `codeatlas:analyze` command (with `--analyzer` filter)
- [ ] `codeatlas:export` command (with `--format` option)
- [ ] `codeatlas:serve` command (dev server)
- [ ] `config/codeatlas.php` publishable config
- [ ] Route registration for embedded web UI
- [ ] Blade view that mounts React app
- [ ] Orchestra Testbench integration tests
- [ ] Documentation

### Basic Web UI
- [ ] App shell with sidebar + canvas + inspector layout
- [ ] Load JSON from API endpoint or file
- [ ] Route list in sidebar
- [ ] Route nodes on React Flow canvas
- [ ] Click node → show properties in inspector
- [ ] Zoom, pan, minimap
- [ ] Basic search/filter
- [ ] Dark/light theme toggle
- [ ] Documentation

### v0.1.0 Release
- [ ] Integration tests (end-to-end: install → scan → analyze → view)
- [ ] README with installation instructions
- [ ] Screenshots/GIFs in README
- [ ] Packagist submission
- [ ] NPM publish (if applicable)

---

## Phase 5 — Middleware + Controller Analyzers (v0.2.0)

### Middleware Analyzer
- [ ] Parse middleware classes
- [ ] Detect aliases, groups, priority
- [ ] Map middleware to routes
- [ ] Middleware flow visualization

### Controller Analyzer
- [ ] Parse controller classes
- [ ] Extract methods, dependencies, traits, return types, attributes
- [ ] Constructor injection detection
- [ ] Controller → Service edges
- [ ] Controller node type for React Flow

---

## Phase 6 — Service + Repository Analyzers (v0.3.0)

### Service Analyzer
- [ ] Detect service classes (convention + interface-based)
- [ ] Extract constructor dependencies
- [ ] Map service → service dependencies
- [ ] Service → repository edges

### Repository Analyzer
- [ ] Detect repository classes
- [ ] Map repository → model
- [ ] Extract query methods

---

## Phase 7 — Model + Database Analyzers (v0.4.0)

### Model Analyzer
- [ ] Relationships (hasMany, belongsTo, etc.)
- [ ] Scopes, casts, fillable, guarded, hidden
- [ ] Observers, events
- [ ] Factories

### Database Analyzer
- [ ] Parse migration files
- [ ] Extract tables, columns, indexes, foreign keys
- [ ] Generate ER diagram data

---

## Phase 8 — Event + Job + Notification Analyzers (v0.5.0)

- [ ] Event → Listener → Job → Notification flow
- [ ] Queue configuration extraction
- [ ] Event flow visualization

---

## Phase 9 — Policy + Schedule + Cache Analyzers (v0.6.0)

- [ ] Policy → Model mapping
- [ ] Schedule extraction from Kernel
- [ ] Cache usage detection

---

## Phase 10 — Full Dependency Graph + Dashboard (v0.7.0)

- [ ] Cross-analyzer dependency resolution
- [ ] Full application architecture graph
- [ ] Architecture dashboard view
- [ ] Statistics and metrics

---

## Phase 11 — Desktop App (v0.8.0)

- [ ] Tauri setup
- [ ] Native file picker
- [ ] Menu bar
- [ ] Auto-updates

---

## Phase 12 — VS Code Extension (v0.9.0)

- [ ] Webview panel with React Flow
- [ ] "Open in CodeAtlas" command
- [ ] File → node navigation

---

## Phase 13 — Stable Release (v1.0.0)

- [ ] API freeze
- [ ] Full documentation site
- [ ] Performance optimization pass
- [ ] Security audit
- [ ] Community contribution guidelines
- [ ] Plugin development guide
