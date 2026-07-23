import { Handle, Position } from '@xyflow/react';
import type { NodeProps, Node } from '@xyflow/react';

import { nodeColor } from '../../../lib/node-colors';
import type { AnalysisNode } from '../../../types/analysis';

export type AtlasFlowNode = Node<{ analysis: AnalysisNode }, 'atlas'>;

/**
 * The standard CodeAtlas graph node per UI_GUIDELINES.md:
 * colored type dot + label, muted secondary line, left accent border.
 */
export function AtlasNode({ data, selected }: NodeProps<AtlasFlowNode>): React.JSX.Element {
  const node = data.analysis;
  const color = nodeColor(node.type);
  const secondary =
    typeof node.metadata['controller'] === 'string'
      ? node.metadata['controller']
      : (node.file?.path ?? '');

  return (
    <div
      className={`min-w-[180px] max-w-[280px] rounded-lg border bg-atlas-bg-tertiary px-3 py-2 shadow-sm ${
        selected ? 'border-atlas-accent' : 'border-atlas-border'
      }`}
      style={{ borderLeftWidth: 3, borderLeftColor: color }}
    >
      <Handle type="target" position={Position.Left} className="!bg-atlas-border" />
      <div className="flex items-center gap-2">
        <span
          aria-hidden
          className="inline-block h-2 w-2 shrink-0 rounded-full"
          style={{ backgroundColor: color }}
        />
        <span className="truncate text-sm font-medium text-atlas-text-primary">{node.label}</span>
      </div>
      {secondary !== '' && (
        <div className="mt-0.5 truncate pl-4 text-xs text-atlas-text-muted">{secondary}</div>
      )}
      <Handle type="source" position={Position.Right} className="!bg-atlas-border" />
    </div>
  );
}
