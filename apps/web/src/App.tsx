import { ReactFlowProvider } from '@xyflow/react';

import { GraphCanvas } from './components/graph/GraphCanvas';
import { Inspector } from './components/inspector/Inspector';
import { LoadScreen } from './components/LoadScreen';
import { Sidebar } from './components/sidebar/Sidebar';
import { useAnalysisStore } from './stores/analysisStore';
import { useUiStore } from './stores/uiStore';

export default function App(): React.JSX.Element {
  const document = useAnalysisStore((s) => s.document);
  const clear = useAnalysisStore((s) => s.clear);
  const theme = useUiStore((s) => s.theme);

  return (
    <div className={theme === 'dark' ? 'dark' : ''}>
      <div className="flex h-screen flex-col bg-atlas-bg-primary text-atlas-text-primary">
        <header className="flex h-11 items-center gap-3 border-b border-atlas-border bg-atlas-bg-secondary px-4">
          <h1 className="text-sm font-semibold tracking-wide">CodeAtlas</h1>
          {document !== null && (
            <>
              <span className="text-xs text-atlas-text-muted">
                {document.project.name ?? 'unnamed project'}
                {document.project.framework !== null &&
                  ` · ${document.project.framework} ${document.project.framework_version ?? ''}`}
              </span>
              <button
                type="button"
                onClick={clear}
                className="ml-auto rounded border border-atlas-border px-2 py-0.5 text-xs text-atlas-text-secondary hover:bg-atlas-bg-tertiary"
              >
                Load another
              </button>
            </>
          )}
        </header>

        <main className="flex min-h-0 flex-1">
          {document === null ? (
            <LoadScreen />
          ) : (
            <ReactFlowProvider>
              <Sidebar />
              <GraphCanvas />
              <Inspector />
            </ReactFlowProvider>
          )}
        </main>
      </div>
    </div>
  );
}
