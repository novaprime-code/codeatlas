import type { ReactElement } from 'react';

/**
 * Phase 0 app shell.
 *
 * This is intentionally a static skeleton — the three-panel layout
 * (sidebar / canvas / inspector) from UI_GUIDELINES.md, with no
 * graph logic yet. Phase 4 replaces the placeholders with the
 * real Sidebar, GraphCanvas (React Flow), and Inspector components.
 */
export default function App(): ReactElement {
  return (
    <div className="flex h-screen flex-col">
      {/* Toolbar */}
      <header className="flex h-12 items-center justify-between border-b border-atlas-border bg-atlas-bg-secondary px-4">
        <div className="flex items-center gap-2">
          <img src="/atlas.svg" alt="" className="h-6 w-6" />
          <span className="font-semibold">CodeAtlas</span>
          <span className="rounded bg-atlas-bg-tertiary px-1.5 py-0.5 text-xs text-atlas-text-muted">
            v0.0.1
          </span>
        </div>
        <div className="text-xs text-atlas-text-muted">Phase 0 — Infrastructure</div>
      </header>

      {/* Main area */}
      <div className="flex flex-1 overflow-hidden">
        {/* Sidebar */}
        <aside className="w-60 shrink-0 border-r border-atlas-border bg-atlas-bg-secondary p-3">
          <div className="text-xs font-medium uppercase tracking-wide text-atlas-text-muted">
            Explorer
          </div>
          <div className="mt-2 text-sm text-atlas-text-secondary">
            Analyzers will appear here after Phase 4.
          </div>
        </aside>

        {/* Canvas */}
        <main className="flex flex-1 items-center justify-center bg-atlas-bg-primary">
          <div className="text-center">
            <div className="text-lg font-semibold">Graph canvas</div>
            <div className="mt-1 text-sm text-atlas-text-secondary">
              React Flow mounts here in Phase 4.
            </div>
          </div>
        </main>

        {/* Inspector */}
        <aside className="w-80 shrink-0 border-l border-atlas-border bg-atlas-bg-secondary p-3">
          <div className="text-xs font-medium uppercase tracking-wide text-atlas-text-muted">
            Inspector
          </div>
          <div className="mt-2 text-sm text-atlas-text-secondary">
            Select a node to see its properties.
          </div>
        </aside>
      </div>

      {/* Console */}
      <footer className="h-8 border-t border-atlas-border bg-atlas-bg-secondary px-4 text-xs leading-8 text-atlas-text-muted">
        Ready
      </footer>
    </div>
  );
}
