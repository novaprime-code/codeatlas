# Jira Setup (If You Choose Jira Over GitHub Projects)

## Project Configuration

### Create Project

- **Name:** CodeAtlas
- **Key:** CATLAS
- **Type:** Scrum (Software)
- **Default Assignee:** Unassigned

### Issue Types

Keep defaults but use them consistently:

- **Epic** → Phase-level grouping (Phase 0, Phase 1, etc.)
- **Story** → User-facing feature (e.g., "As a developer, I can see my routes as a graph")
- **Task** → Implementation work (e.g., "Implement RouteAnalyzer class")
- **Sub-task** → Granular steps within a task
- **Bug** → Something broken

### Custom Fields (Optional)

- **Package:** Dropdown — contracts, core, scanner, laravel, analyzer-routes, etc.
- **Definition of Done:** Checklist
- **Benchmark Required:** Boolean

### Board Configuration

**Columns:**

1. Backlog
2. Selected for Sprint
3. In Progress
4. In Review
5. Done

### Workflow

```
Backlog → Selected → In Progress → In Review → Done
                         ↓              ↓
                      Blocked        Changes Requested
                         ↓              ↓
                   In Progress     In Progress
```

### Sprint Setup

- **Sprint duration:** 1 week
- **Sprint naming:** Sprint 0.1, Sprint 0.2, etc.
- **Velocity tracking:** Story points (use T-shirt sizing: S=1, M=3, L=5, XL=8)

### Labels (Create These)

```
phase:0-infra
phase:1-core
phase:2-scanner
phase:3-routes
phase:4-mvp
phase:5-controllers
phase:6-services
phase:7-models
phase:8-events
phase:9-policies
phase:10-deps
phase:11-desktop
pkg:contracts
pkg:core
pkg:scanner
pkg:laravel
pkg:analyzer-routes
pkg:analyzer-controllers
pkg:analyzer-middleware
pkg:analyzer-services
pkg:analyzer-repositories
pkg:analyzer-models
pkg:analyzer-events
pkg:analyzer-jobs
pkg:analyzer-policies
pkg:analyzer-schedule
pkg:exporter-json
pkg:ui
pkg:desktop
```

### Components (Create These)

- Contracts
- Core
- Scanner
- Laravel Bridge
- Route Analyzer
- Controller Analyzer
- Middleware Analyzer
- Service Analyzer
- Repository Analyzer
- Model Analyzer
- Event Analyzer
- Job Analyzer
- Policy Analyzer
- Schedule Analyzer
- JSON Exporter
- Web UI
- Desktop App
- CI/CD
- Documentation

### Versions (Create These)

- v0.0.1 — Foundation
- v0.1.0 — First Light
- v0.2.0 — Connections
- v0.3.0 — Depth
- v0.4.0 — Relationships
- v0.5.0 — Flow
- v0.6.0 — Authority
- v0.7.0 — Big Picture
- v0.8.0 — Desktop
- v0.9.0 — Editor
- v1.0.0 — Atlas

## Importing the CSV

1. Go to your Jira project
2. Click **Project Settings** → **Import Issues** → **CSV**
3. Upload `TICKETS_BACKLOG.csv`
4. Map columns:
    - Summary → Summary
    - Issue Type → Issue Type
    - Priority → Priority
    - Labels → Labels (multi-value, comma-separated)
    - Milestone → Fix Version
    - Description → Description
    - Acceptance Criteria → Description (append)
5. Import

**Note:** Jira CSV import can be finicky. If labels don't import, add them manually to the first few issues and bulk-edit the rest.

## My Honest Recommendation

If you're working solo or with 1-2 people, GitHub Projects gives you 90% of what you need with 10% of the overhead. Use Jira only if:

- Your university or employer already uses it
- You want to practice Jira for job interviews
- You plan to grow the team beyond 5 people

For a solo open-source project, the best tool is the one that doesn't slow you down.
