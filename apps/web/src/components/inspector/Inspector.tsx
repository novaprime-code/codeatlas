import { useMemo } from 'react';

import { useAnalysisStore } from '../../stores/analysisStore';
import { useGraphStore } from '../../stores/graphStore';
import { nodeColor } from '../../lib/node-colors';
import { synthesizeNode } from '../../lib/graph-layout';
import { isRouteMetadata } from '../../types/analysis';
import type { AnalysisNode } from '../../types/analysis';

function Row({ label, value }: { label: string; value: string }): React.JSX.Element {
  return (
    <div className="flex justify-between gap-3 py-1 text-sm">
      <span className="shrink-0 text-atlas-text-muted">{label}</span>
      <span className="truncate text-right font-mono text-atlas-text-primary" title={value}>
        {value}
      </span>
    </div>
  );
}

/**
 * Right panel: properties of the selected node.
 * Route nodes get a dedicated, typed detail view; everything else
 * falls back to generic metadata rows.
 */
export function Inspector(): React.JSX.Element {
  const document = useAnalysisStore((s) => s.document);
  const selectedNodeId = useGraphStore((s) => s.selectedNodeId);

  const node: AnalysisNode | null = useMemo(() => {
    if (document === null || selectedNodeId === null) {
      return null;
    }

    return (
      document.graph.nodes.find((n) => n.id === selectedNodeId) ??
      synthesizeNode(selectedNodeId)
    );
  }, [document, selectedNodeId]);

  const connections = useMemo(() => {
    if (document === null || node === null) {
      return { outgoing: [], incoming: [] };
    }

    return {
      outgoing: document.graph.edges.filter((e) => e.source === node.id),
      incoming: document.graph.edges.filter((e) => e.target === node.id),
    };
  }, [document, node]);

  if (node === null) {
    return (
      <aside
        aria-label="Inspector"
        className="w-80 min-w-80 border-l border-atlas-border bg-atlas-bg-secondary p-4"
      >
        <p className="text-sm text-atlas-text-muted">Select a node to inspect it.</p>
      </aside>
    );
  }

  const meta = node.metadata;

  return (
    <aside
      aria-label="Inspector"
      className="flex w-80 min-w-80 flex-col overflow-y-auto border-l border-atlas-border bg-atlas-bg-secondary"
    >
      <header className="border-b border-atlas-border p-4">
        <div className="flex items-center gap-2">
          <span
            aria-hidden
            className="inline-block h-2.5 w-2.5 rounded-full"
            style={{ backgroundColor: nodeColor(node.type) }}
          />
          <h2 className="truncate text-base font-semibold text-atlas-text-primary">{node.label}</h2>
        </div>
        <p className="mt-0.5 text-xs capitalize text-atlas-text-secondary">
          {node.type.replaceAll('_', ' ')}
        </p>
      </header>

      <section aria-label="Properties" className="border-b border-atlas-border p-4">
        <h3 className="mb-2 text-xs font-semibold uppercase tracking-wide text-atlas-text-muted">
          Properties
        </h3>

        {isRouteMetadata(meta) ? (
          <>
            <Row label="URI" value={meta.uri} />
            <Row label="Methods" value={meta.methods.join(', ')} />
            {meta.name !== null && <Row label="Name" value={meta.name} />}
            {meta.controller !== null && <Row label="Controller" value={meta.controller} />}
            {meta.action !== null && <Row label="Action" value={meta.action} />}
            {meta.is_closure && <Row label="Handler" value="Closure" />}
            {meta.middleware.length > 0 && (
              <Row label="Middleware" value={meta.middleware.join(', ')} />
            )}
            {meta.parameters.length > 0 && (
              <Row label="Parameters" value={meta.parameters.join(', ')} />
            )}
            {Object.keys(meta.where).length > 0 && (
              <Row
                label="Constraints"
                value={Object.entries(meta.where)
                  .map(([k, v]) => `${k}: ${v}`)
                  .join(', ')}
              />
            )}
          </>
        ) : (
          Object.entries(meta)
            .filter(([, v]) => typeof v === 'string' || typeof v === 'number')
            .map(([key, value]) => <Row key={key} label={key} value={String(value)} />)
        )}

        {node.file !== null && <Row label="File" value={node.file.path} />}
      </section>

      {(connections.outgoing.length > 0 || connections.incoming.length > 0) && (
        <section aria-label="Connections" className="p-4">
          <h3 className="mb-2 text-xs font-semibold uppercase tracking-wide text-atlas-text-muted">
            Connections
          </h3>
          {connections.outgoing.map((edge) => (
            <div key={edge.id} className="truncate py-0.5 text-sm text-atlas-text-secondary">
              <span aria-hidden>→ </span>
              {edge.target}
            </div>
          ))}
          {connections.incoming.map((edge) => (
            <div key={edge.id} className="truncate py-0.5 text-sm text-atlas-text-secondary">
              <span aria-hidden>← </span>
              {edge.source}
            </div>
          ))}
        </section>
      )}
    </aside>
  );
}
