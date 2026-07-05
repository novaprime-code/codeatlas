# CodeAtlas — Current Tasks

> This file tracks the active sprint. Update after every completed task.

## Active Phase: Phase 0 — Infrastructure

### Current Sprint: Sprint 0.1 — Repository Skeleton

**Priority Order:** Complete tasks in this exact sequence.

---

### Task 0.1.1 — Root Repository Setup
**Status:** ⬜ Not Started
**Scope:** Root-level files only

Create:
- `composer.json` (root, type: project, monorepo config)
- `package.json` (root, PNPM workspaces)
- `pnpm-workspace.yaml`
- `turbo.json`
- `Makefile`
- `.gitignore`
- `.editorconfig`
- `LICENSE` (MIT)
- `README.md` (minimal, will expand later)

Acceptance Criteria:
- [ ] `composer install` succeeds
- [ ] `pnpm install` succeeds
- [ ] `make help` shows available commands
- [ ] `.gitignore` covers vendor, node_modules, .env, IDE files, build artifacts

---

### Task 0.1.2 — PHP Tooling Configuration
**Status:** ⬜ Not Started
**Scope:** Root-level PHP tooling

Create:
- `pint.json` (PER Coding Style 2.0 preset)
- `phpstan.neon.dist` (level max)
- `rector.php` (PHP 8.3 target, quality rules)
- `phpunit.xml.dist` or `phpunit.xml` (Pest uses this)

Acceptance Criteria:
- [ ] `./vendor/bin/pint --test` passes (no files to check yet, but config valid)
- [ ] `./vendor/bin/phpstan` runs without config errors
- [ ] `./vendor/bin/pest` runs (0 tests, 0 assertions is OK)

---

### Task 0.1.3 — Contracts Package Skeleton
**Status:** ⬜ Not Started
**Scope:** `packages/contracts/`

Create:
- `packages/contracts/composer.json`
- `packages/contracts/src/` (empty, with `.gitkeep`)
- `packages/contracts/tests/Pest.php`
- `packages/contracts/README.md`

Acceptance Criteria:
- [ ] Composer autoload resolves `CodeAtlas\Contracts\` namespace
- [ ] Pest runs in this package (0 tests OK)

---

### Task 0.1.4 — Core Package Skeleton
**Status:** ⬜ Not Started
**Scope:** `packages/core/`

Create:
- `packages/core/composer.json` (requires `codeatlas/contracts`)
- `packages/core/src/` (empty, with `.gitkeep`)
- `packages/core/tests/Pest.php`
- `packages/core/README.md`

Acceptance Criteria:
- [ ] Composer autoload resolves `CodeAtlas\Core\` namespace
- [ ] Pest runs in this package

---

### Task 0.1.5 — Scanner Package Skeleton
**Status:** ⬜ Not Started
**Scope:** `packages/scanner/`

Create:
- `packages/scanner/composer.json` (requires `codeatlas/contracts`, `codeatlas/core`)
- `packages/scanner/src/`
- `packages/scanner/tests/Pest.php`
- `packages/scanner/README.md`

Acceptance Criteria:
- [ ] Composer autoload resolves `CodeAtlas\Scanner\` namespace
- [ ] Pest runs in this package

---

### Task 0.1.6 — Laravel Bridge Package Skeleton
**Status:** ⬜ Not Started
**Scope:** `packages/laravel/`

Create:
- `packages/laravel/composer.json` (requires `codeatlas/core`, `codeatlas/scanner`, `laravel/framework`)
- `packages/laravel/src/`
- `packages/laravel/tests/Pest.php`
- `packages/laravel/config/codeatlas.php` (empty config stub)
- `packages/laravel/README.md`

Acceptance Criteria:
- [ ] Composer autoload resolves `CodeAtlas\Laravel\` namespace
- [ ] Pest runs in this package

---

### Task 0.1.7 — Route Analyzer Package Skeleton
**Status:** ⬜ Not Started
**Scope:** `packages/analyzers/routes/`

Create:
- `packages/analyzers/routes/composer.json` (requires `codeatlas/contracts`, `codeatlas/core`)
- `packages/analyzers/routes/src/`
- `packages/analyzers/routes/tests/Pest.php`
- `packages/analyzers/routes/README.md`

Acceptance Criteria:
- [ ] Composer autoload resolves `CodeAtlas\Analyzers\Routes\` namespace
- [ ] Pest runs in this package

---

### Task 0.1.8 — JSON Exporter Package Skeleton
**Status:** ⬜ Not Started
**Scope:** `packages/exporters/json/`

Create:
- `packages/exporters/json/composer.json` (requires `codeatlas/contracts`, `codeatlas/core`)
- `packages/exporters/json/src/`
- `packages/exporters/json/tests/Pest.php`
- `packages/exporters/json/README.md`

Acceptance Criteria:
- [ ] Composer autoload resolves `CodeAtlas\Exporters\Json\` namespace
- [ ] Pest runs in this package

---

### Task 0.1.9 — Frontend Skeleton
**Status:** ⬜ Not Started
**Scope:** `apps/web/`

Create:
- `apps/web/` via `pnpm create vite` (React + TypeScript)
- Install: Tailwind, shadcn/ui, React Flow, Monaco Editor, TanStack Query, Zustand
- Configure: ESLint (strict TS), Prettier
- Create: `apps/web/src/App.tsx` (minimal shell)
- Create: `apps/web/src/components/` directory structure

Acceptance Criteria:
- [ ] `pnpm dev` starts the dev server
- [ ] `pnpm build` produces a production build
- [ ] `pnpm lint` passes
- [ ] `pnpm test` runs (0 tests OK)
- [ ] Tailwind utility classes work
- [ ] TypeScript strict mode enabled

---

### Task 0.1.10 — CI Pipeline
**Status:** ⬜ Not Started
**Scope:** `.github/workflows/`

Create:
- `.github/workflows/ci.yml` — runs on push and PR
  - PHP matrix: 8.3, 8.4
  - Steps: composer install, pint --test, phpstan, pest
  - Frontend: pnpm install, lint, build, test
- `.github/ISSUE_TEMPLATE/bug.yml`
- `.github/ISSUE_TEMPLATE/feature.yml`
- `.github/ISSUE_TEMPLATE/task.yml`
- `.github/PULL_REQUEST_TEMPLATE.md`

Acceptance Criteria:
- [ ] CI runs on push to `main` and on PRs
- [ ] All checks pass on the skeleton repo
- [ ] Issue templates render correctly on GitHub

---

### Task 0.1.11 — Monorepo Orchestration
**Status:** ⬜ Not Started
**Scope:** Root-level build tooling

Configure:
- `turbo.json` with `build`, `test`, `lint` pipelines
- `Makefile` targets: `install`, `test`, `lint`, `analyze`, `format`, `clean`, `build`
- Verify all commands work end-to-end

Acceptance Criteria:
- [ ] `make install` installs PHP + Node dependencies
- [ ] `make test` runs all PHP + frontend tests
- [ ] `make lint` runs Pint + PHPStan + ESLint
- [ ] `make build` builds frontend
- [ ] `turbo run test` runs tests across all packages

---

### Task 0.1.12 — Git Hooks + Commit Enforcement
**Status:** ⬜ Not Started
**Scope:** Root-level git configuration

Configure:
- Husky for pre-commit hooks
- lint-staged (run Pint on staged `.php`, ESLint on staged `.ts`/`.tsx`)
- Commitlint with conventional commit rules

Acceptance Criteria:
- [ ] Committing with a non-conventional message is rejected
- [ ] Staged PHP files are auto-formatted on commit
- [ ] Staged TS files are auto-linted on commit

---

## Completed Tasks

(none yet)

---

## Backlog (Next Sprint)

- Task 1.1.1 — Implement Contracts: `AnalyzerInterface`
- Task 1.1.2 — Implement Contracts: `ScannerInterface`
- Task 1.1.3 — Implement Contracts: `ExporterInterface`
- Task 1.1.4 — Implement Contracts: Node/Edge/Graph interfaces
- Task 1.1.5 — Implement Contracts: Enums (NodeType, EdgeType, FileType)
- Task 1.1.6 — Implement Contracts: Value objects (ProjectContext, AnalysisResult, FileReference)
- Task 1.1.7 — Implement Contracts: Exception hierarchy
- Task 1.2.1 — Implement Core: Container
- Task 1.2.2 — Implement Core: Configuration
- Task 1.2.3 — Implement Core: Plugin Loader
- Task 1.2.4 — Implement Core: Event Bus
- Task 1.2.5 — Implement Core: Logger
- Task 1.2.6 — Implement Core: Pipeline Runner
- Task 1.2.7 — Implement Core: Parser wrapper
