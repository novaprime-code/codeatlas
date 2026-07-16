# Changelog

All notable changes to CodeAtlas will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added — Phase 1 Contracts (Sprint 1.1)

- **Core interfaces:** `AnalyzerInterface`, `ScannerInterface`, `ExporterInterface`, `ParserInterface`, `ParsedFileInterface`, `PluginInterface`, `ContainerInterface`, `ConfigInterface`
- **Enums:** `NodeType` (23 cases), `EdgeType` (15 cases), `FileType` (19 cases), `Severity` (4 cases)
- **Graph primitives:** `Node`, `Edge`, `Graph` with ID-based deduplication and idempotent merge
- **Value objects:** `FileReference`, `ScanConfig`, `ProjectContext`, `AnalysisResult`, `AnalysisError`, `ExportConfig`, `ExportOutput`
- **Exception hierarchy:** `CodeAtlasException` base plus 7 typed exceptions with named constructors
- **Test suite:** 15 test files covering enums, graph primitives, value objects, and exception hierarchy

### Added — Phase 0 Infrastructure (Sprint 0.1)

- Monorepo scaffolding (Composer path repos + PNPM workspaces + Turborepo)
- All 6 PHP package skeletons: contracts, core, scanner, laravel, analyzer-routes, exporter-json
- Frontend skeleton: Vite + React + TypeScript strict + Tailwind + React Flow + Monaco + TanStack Query + Zustand
- Tooling: Laravel Pint (PER preset + strict types), PHPStan level max, Rector PHP 8.3, Pest
- GitHub Actions CI matrix (PHP 8.3 + 8.4, frontend build/test/lint)
- Husky pre-commit hooks, lint-staged, Commitlint with per-package scopes
- VS Code workspace settings and extension recommendations
- Issue templates, PR template, project management setup (GitHub + Jira)
