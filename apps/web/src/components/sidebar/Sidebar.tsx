import { useMemo, useState } from 'react';

import { useAnalysisStore } from '../../stores/analysisStore';
import { useGraphStore } from '../../stores/graphStore';
import { nodeColor } from '../../lib/node-colors';
import type { AnalysisNode, NodeType } from '../../types/analysis';

/**
 * Left panel: every graph node grouped by type, with counts and search.
 * Clicking an item selects the node (centering is handled by the canvas).
 */
export function Sidebar(): React.JSX.Element {
  const document = useAnalysisStore((s) => s.document);
  const selectedNodeId = useGraphStore((s) => s.selectedNodeId);
  const selectNode = useGraphStore((s) => s.selectNode);
  const [query, setQuery] = useState('');
  const [collapsed, setCollapsed] = useState<Record<string, boolean>>({});

  const groups = useMemo(() => {
    const nodes = document?.graph.nodes ?? [];
    const filtered =
      query === ''
        ? nodes
        : nodes.filter((n) => n.label.toLowerCase().includes(query.toLowerCase()));

    const byType = new Map<NodeType, AnalysisNode[]>();
    for (const node of filtered) {
      const bucket = byType.get(node.type) ?? [];
      bucket.push(node);
      byType.set(node.type, bucket);
    }

    return [...byType.entries()].sort(([a], [b]) => a.localeCompare(b));
  }, [document, query]);

  return (
    <aside
      aria-label="Node list"
      className="flex h-full w-60 min-w-60 flex-col border-r border-atlas-border bg-atlas-bg-secondary"
    >
      <div className="p-2">
        <input
          type="search"
          value={query}
          onChange={(e) => {
            setQuery(e.target.value);
          }}
          placeholder="Search…"
          aria-label="Search nodes"
          className="w-full rounded-md border border-atlas-border bg-atlas-bg-primary px-2 py-1.5 text-sm text-atlas-text-primary placeholder:text-atlas-text-muted focus:border-atlas-border-active focus:outline-none"
        />
      </div>

      <nav className="flex-1 overflow-y-auto px-1 pb-2">
        {groups.length === 0 && (
          <p className="px-2 py-4 text-sm text-atlas-text-muted">
            {document === null ? 'Load an analysis to begin.' : 'No matching nodes.'}
          </p>
        )}

        {groups.map(([type, nodes]) => {
          const isCollapsed = collapsed[type] ?? false;

          return (
            <section key={type} aria-label={type}>
              <button
                type="button"
                onClick={() => {
                  setCollapsed((c) => ({ ...c, [type]: !isCollapsed }));
                }}
                className="flex w-full items-center gap-2 rounded px-2 py-1.5 text-left text-sm font-semibold text-atlas-text-secondary hover:bg-atlas-bg-tertiary"
              >
                <span aria-hidden className="text-xs">
                  {isCollapsed ? '▸' : '▾'}
                </span>
                <span
                  aria-hidden
                  className="inline-block h-2 w-2 rounded-full"
                  style={{ backgroundColor: nodeColor(type) }}
                />
                <span className="capitalize">{type.replaceAll('_', ' ')}s</span>
                <span className="ml-auto rounded bg-atlas-bg-tertiary px-1.5 text-xs text-atlas-text-muted">
                  {nodes.length}
                </span>
              </button>

              {!isCollapsed &&
                nodes.map((node) => (
                  <button
                    key={node.id}
                    type="button"
                    onClick={() => {
                      selectNode(node.id);
                    }}
                    className={`block w-full truncate rounded px-6 py-1 text-left text-sm ${
                      node.id === selectedNodeId
                        ? 'bg-atlas-accent-subtle text-atlas-text-primary'
                        : 'text-atlas-text-secondary hover:bg-atlas-bg-tertiary'
                    }`}
                  >
                    {node.label}
                  </button>
                ))}
            </section>
          );
        })}
      </nav>
    </aside>
  );
}
