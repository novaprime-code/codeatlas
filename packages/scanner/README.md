# codeatlas/scanner

Project file discovery. Given a path, returns a `ProjectContext` with every analyzable file classified by type.

**Discovery only — no parsing.** Parsing happens in analyzers via the core parser.

## Features

- Symfony Finder with lazy iteration (memory-safe on huge projects)
- Configurable scan paths and glob exclusions
- File classification by path convention
- Laravel framework detection
- composer.json metadata extraction

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
