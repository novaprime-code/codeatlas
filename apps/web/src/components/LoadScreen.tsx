import { useCallback, useState } from 'react';

import { readAnalysisFile } from '../lib/analysis-loader';
import { useAnalysisStore } from '../stores/analysisStore';

/**
 * Shown until an analysis document is loaded. Accepts drag-and-drop or
 * file picker. (URL loading arrives with codeatlas:serve in a later sprint.)
 */
export function LoadScreen(): React.JSX.Element {
  const setDocument = useAnalysisStore((s) => s.setDocument);
  const loadError = useAnalysisStore((s) => s.loadError);
  const setLoadError = useAnalysisStore((s) => s.setLoadError);
  const [dragging, setDragging] = useState(false);

  const loadFile = useCallback(
    (file: File | undefined) => {
      if (file === undefined) {
        return;
      }

      readAnalysisFile(file)
        .then(setDocument)
        .catch((e: unknown) => {
          setLoadError(e instanceof Error ? e.message : 'Failed to read the file.');
        });
    },
    [setDocument, setLoadError],
  );

  return (
    <div
      className={`flex h-full flex-1 flex-col items-center justify-center gap-4 border-2 border-dashed transition-colors ${
        dragging ? 'border-atlas-accent bg-atlas-accent-subtle' : 'border-transparent'
      }`}
      onDragOver={(e) => {
        e.preventDefault();
        setDragging(true);
      }}
      onDragLeave={() => {
        setDragging(false);
      }}
      onDrop={(e) => {
        e.preventDefault();
        setDragging(false);
        loadFile(e.dataTransfer.files[0]);
      }}
    >
      <h2 className="text-lg font-semibold text-atlas-text-primary">Load an analysis</h2>
      <p className="max-w-md text-center text-sm text-atlas-text-secondary">
        Run <code className="font-mono text-atlas-accent">php artisan codeatlas:analyze</code> in
        your Laravel project, then drop the generated{' '}
        <code className="font-mono">codeatlas-analysis.json</code> here.
      </p>

      <label className="cursor-pointer rounded-md bg-atlas-accent px-4 py-2 text-sm font-medium text-white hover:bg-atlas-accent-hover">
        Choose file
        <input
          type="file"
          accept=".json,application/json"
          className="hidden"
          aria-label="Analysis JSON file"
          onChange={(e) => {
            loadFile(e.target.files?.[0]);
          }}
        />
      </label>

      {loadError !== null && (
        <p role="alert" className="text-sm text-atlas-error">
          {loadError}
        </p>
      )}
    </div>
  );
}
