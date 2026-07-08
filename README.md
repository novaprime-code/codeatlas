<p align="center">
  <h1 align="center">CodeAtlas</h1>
  <p align="center"><strong>Visual Architecture Explorer</strong><br>See your codebase. Understand your architecture.</p>
</p>

<p align="center">
  <img alt="Status" src="https://img.shields.io/badge/status-under%20development-orange">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white">
  <img alt="License" src="https://img.shields.io/badge/license-MIT-blue">
</p>

---

CodeAtlas performs **static analysis** on a codebase and produces a **visual, interactive architecture map**. Open a project and instead of reading hundreds of files, see the whole picture: routes, controllers, services, repositories, models, events, jobs, policies, and how everything connects.

> **Laravel is the first supported framework.** The core is framework-agnostic by design.

## What makes it different

| Tool          | Purpose                        |
| ------------- | ------------------------------ |
| Telescope     | Runtime debugging              |
| Horizon       | Queue monitoring               |
| Pulse         | Performance metrics            |
| PHP Insights  | Code quality                   |
| **CodeAtlas** | **Architecture understanding** |

CodeAtlas never executes your code. It never modifies your code. It reads, analyzes, and visualizes — that's it.

## How it works

```
Source Code → Scanner → AST Parser → Analyzer → JSON → Interactive Graph
```

- **AST-based** — powered by [nikic/php-parser](https://github.com/nikic/PHP-Parser), never regex
- **Plugin architecture** — every analyzer is an independent, replaceable package
- **JSON contract** — the UI only ever consumes JSON, never touches PHP

## Tech stack

**Backend:** PHP 8.3+, nikic/php-parser, Symfony Finder, Pest, PHPStan
**Frontend:** React, TypeScript, Vite, Tailwind, React Flow, Monaco Editor
**Desktop:** Tauri

## Status

🚧 **Under active development.** Follow the [roadmap](.claude/ROADMAP.md) for progress. First release (v0.1.0) will include the Route Visualizer.

## Development

```bash
git clone https://github.com/novaprime-code/codeatlas.git
cd codeatlas
make install    # composer + pnpm install
make check      # lint + analyze + test
make help       # see all commands

```

See [CONTRIBUTING.md](.claude/CONTRIBUTING.md) for the full development workflow.

## License

MIT © [Snova Labs](https://github.com/novaprime-code)
