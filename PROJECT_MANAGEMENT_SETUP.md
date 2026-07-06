# CodeAtlas — Project Management Setup

## Recommended: GitHub Projects (Free, Integrated)

### Why GitHub Projects over Jira

| Factor          | GitHub Projects                     | Jira                             |
| --------------- | ----------------------------------- | -------------------------------- |
| Cost            | Free                                | Free tier limited, paid for full |
| Setup time      | 5 minutes                           | 30+ minutes                      |
| Integration     | Native to repo                      | Requires app install             |
| Contributors    | Already have accounts               | Need separate accounts           |
| Issue linking   | Automatic (PR → Issue)              | Manual or via integration        |
| Public backlog  | Built-in                            | Requires public board config     |
| Overhead        | Minimal                             | Significant (workflows, schemes) |
| AI-assisted dev | Claude reads GitHub issues natively | Extra context needed             |

### GitHub Setup Instructions

#### Step 1: Create Labels

Run this in your terminal after creating the repo:

```bash
#!/bin/bash
REPO="YOUR_ORG/codeatlas"

# Priority labels
gh label create "P0-critical" --color "b60205" --description "Must fix immediately"
gh label create "P1-high" --color "d93f0b" --description "Must complete this sprint"
gh label create "P2-medium" --color "fbca04" --description "Should complete this milestone"
gh label create "P3-low" --color "0e8a16" --description "Nice to have"

# Type labels
gh label create "type:epic" --color "3E4B9E" --description "Epic — large feature group"
gh label create "type:story" --color "5319E7" --description "Story — user-facing feature"
gh label create "type:task" --color "0075CA" --description "Task — implementation work"
gh label create "type:bug" --color "D73A4A" --description "Bug — something broken"
gh label create "type:chore" --color "EDEDED" --description "Chore — tooling, CI, config"
gh label create "type:docs" --color "0075CA" --description "Documentation"
gh label create "type:test" --color "BFD4F2" --description "Testing"
gh label create "type:perf" --color "F9D0C4" --description "Performance improvement"
gh label create "type:refactor" --color "D4C5F9" --description "Refactoring"

# Package labels
gh label create "pkg:contracts" --color "C2E0C6" --description "packages/contracts"
gh label create "pkg:core" --color "C2E0C6" --description "packages/core"
gh label create "pkg:scanner" --color "C2E0C6" --description "packages/scanner"
gh label create "pkg:laravel" --color "C2E0C6" --description "packages/laravel"
gh label create "pkg:analyzer-routes" --color "BFDADC" --description "packages/analyzers/routes"
gh label create "pkg:analyzer-controllers" --color "BFDADC" --description "packages/analyzers/controllers"
gh label create "pkg:analyzer-middleware" --color "BFDADC" --description "packages/analyzers/middleware"
gh label create "pkg:analyzer-services" --color "BFDADC" --description "packages/analyzers/services"
gh label create "pkg:analyzer-repositories" --color "BFDADC" --description "packages/analyzers/repositories"
gh label create "pkg:analyzer-models" --color "BFDADC" --description "packages/analyzers/models"
gh label create "pkg:analyzer-events" --color "BFDADC" --description "packages/analyzers/events"
gh label create "pkg:analyzer-jobs" --color "BFDADC" --description "packages/analyzers/jobs"
gh label create "pkg:analyzer-policies" --color "BFDADC" --description "packages/analyzers/policies"
gh label create "pkg:analyzer-schedule" --color "BFDADC" --description "packages/analyzers/schedule"
gh label create "pkg:analyzer-notifications" --color "BFDADC" --description "packages/analyzers/notifications"
gh label create "pkg:analyzer-cache" --color "BFDADC" --description "packages/analyzers/cache"
gh label create "pkg:analyzer-dependencies" --color "BFDADC" --description "packages/analyzers/dependencies"
gh label create "pkg:exporter-json" --color "D4C5F9" --description "packages/exporters/json"
gh label create "pkg:exporter-mermaid" --color "D4C5F9" --description "packages/exporters/mermaid"
gh label create "pkg:ui" --color "FEF2C0" --description "apps/web (React UI)"
gh label create "pkg:desktop" --color "FEF2C0" --description "apps/desktop (Tauri)"

# Phase labels
gh label create "phase:0-infra" --color "E6E6E6" --description "Phase 0 — Infrastructure"
gh label create "phase:1-core" --color "E6E6E6" --description "Phase 1 — Core + Contracts"
gh label create "phase:2-scanner" --color "E6E6E6" --description "Phase 2 — Scanner"
gh label create "phase:3-routes" --color "E6E6E6" --description "Phase 3 — Route Analyzer"
gh label create "phase:4-mvp" --color "E6E6E6" --description "Phase 4 — Laravel Bridge + UI MVP"
gh label create "phase:5-controllers" --color "E6E6E6" --description "Phase 5 — Middleware + Controllers"
gh label create "phase:6-services" --color "E6E6E6" --description "Phase 6 — Services + Repositories"
gh label create "phase:7-models" --color "E6E6E6" --description "Phase 7 — Models + Database"
gh label create "phase:8-events" --color "E6E6E6" --description "Phase 8 — Events + Jobs"
gh label create "phase:9-policies" --color "E6E6E6" --description "Phase 9 — Policies + Schedule"
gh label create "phase:10-deps" --color "E6E6E6" --description "Phase 10 — Dependency Graph"
gh label create "phase:11-desktop" --color "E6E6E6" --description "Phase 11 — Desktop App"

# Status labels (for non-board tracking)
gh label create "status:blocked" --color "B60205" --description "Blocked by another issue"
gh label create "status:needs-review" --color "FBCA04" --description "Ready for review"
gh label create "status:in-progress" --color "0E8A16" --description "Currently being worked on"

# Remove default labels
gh label delete "bug" --yes 2>/dev/null
gh label delete "documentation" --yes 2>/dev/null
gh label delete "duplicate" --yes 2>/dev/null
gh label delete "enhancement" --yes 2>/dev/null
gh label delete "good first issue" --yes 2>/dev/null
gh label delete "help wanted" --yes 2>/dev/null
gh label delete "invalid" --yes 2>/dev/null
gh label delete "question" --yes 2>/dev/null
gh label delete "wontfix" --yes 2>/dev/null
```

#### Step 2: Create Milestones

```bash
REPO="YOUR_ORG/codeatlas"

gh api repos/$REPO/milestones -f title="v0.0.1 — Foundation" -f description="Infrastructure, CI, tooling. Zero features." -f state="open"
gh api repos/$REPO/milestones -f title="v0.1.0 — First Light" -f description="Core, Contracts, Scanner, Route Analyzer, JSON Exporter, Basic Viewer"
gh api repos/$REPO/milestones -f title="v0.2.0 — Connections" -f description="Middleware Analyzer, Controller Analyzer"
gh api repos/$REPO/milestones -f title="v0.3.0 — Depth" -f description="Service Analyzer, Repository Analyzer"
gh api repos/$REPO/milestones -f title="v0.4.0 — Relationships" -f description="Model Analyzer, Database/Migration Analyzer"
gh api repos/$REPO/milestones -f title="v0.5.0 — Flow" -f description="Event Analyzer, Job Analyzer, Notification Analyzer"
gh api repos/$REPO/milestones -f title="v0.6.0 — Authority" -f description="Policy Analyzer, Schedule Analyzer, Cache Analyzer"
gh api repos/$REPO/milestones -f title="v0.7.0 — Big Picture" -f description="Full Dependency Graph, Architecture Dashboard"
gh api repos/$REPO/milestones -f title="v0.8.0 — Desktop" -f description="Tauri desktop application"
gh api repos/$REPO/milestones -f title="v0.9.0 — Editor" -f description="VS Code extension"
gh api repos/$REPO/milestones -f title="v1.0.0 — Atlas" -f description="Stable release, public API freeze"
```

#### Step 3: Create GitHub Project Board

```bash
# Create project
gh project create --owner YOUR_ORG --title "CodeAtlas Development" --format board

# Columns (created in the web UI or via API):
# Backlog | Todo | In Progress | Review | Done
```

#### Step 4: Issue Templates

Already defined in `.github/ISSUE_TEMPLATE/` — see the issue template files below.
