import { useEffect, useMemo } from 'react';
import { Background, Controls, MiniMap, ReactFlow, useReactFlow } from '@xyflow/react';
import type { NodeMouseHandler } from '@xyflow/react';

import '@xyflow/react/dist/style.css';

import { useAnalysisStore } from '../../stores/analysisStore';
import { useGraphStore } from '../../stores/graphStore';
import { layoutGraph } from '../../lib/graph-layout';
import { AtlasNode } from './nodes/AtlasNode';
import type { AtlasFlowNode } from './nodes/AtlasNode';

const nodeTypes = { atlas: AtlasNode };

/**
 * Center panel: the architecture graph (React Flow).
 * Selecting a node in the sidebar centers the viewport on it.
 */
export function GraphCanvas(): React.JSX.Element {
  const document = useAnalysisStore((s) => s.document);
  const selectedNodeId = useGraphStore((s) => s.selectedNodeId);
  const selectNode = useGraphStore((s) => s.selectNode);
  const { fitView, setCenter, getNode } = useReactFlow();

  const { flowNodes, flowEdges } = useMemo(() => {
    if (document === null) {
      return { flowNodes: [], flowEdges: [] };
    }

    return layoutGraph(document.graph.nodes, document.graph.edges);
  }, [document]);

  const nodesWithSelection = useMemo(
    () =>
      flowNodes.map((n) => ({
        ...n,
        selected: n.id === selectedNodeId,
      })),
    [flowNodes, selectedNodeId],
  );

  useEffect(() => {
    if (selectedNodeId === null) {
      return;
    }

    const node = getNode(selectedNodeId);
    if (node !== undefined) {
      void setCenter(node.position.x + 140, node.position.y + 30, { zoom: 1.2, duration: 300 });
    }
  }, [selectedNodeId, getNode, setCenter]);

  useEffect(() => {
    if (flowNodes.length > 0) {
      void fitView({ padding: 0.15 });
    }
  }, [flowNodes.length, fitView]);

  const onNodeClick: NodeMouseHandler<AtlasFlowNode> = (_event, node) => {
    selectNode(node.id);
  };

  return (
    <div className="h-full flex-1 bg-atlas-bg-primary" data-testid="graph-canvas">
      <ReactFlow
        nodes={nodesWithSelection}
        edges={flowEdges}
        nodeTypes={nodeTypes}
        onNodeClick={onNodeClick}
        onPaneClick={() => {
          selectNode(null);
        }}
        proOptions={{ hideAttribution: true }}
        minZoom={0.2}
        fitView
      >
        <Background gap={24} />
        <Controls showInteractive={false} />
        <MiniMap pannable zoomable className="!bg-atlas-bg-secondary" />
      </ReactFlow>
    </div>
  );
}
