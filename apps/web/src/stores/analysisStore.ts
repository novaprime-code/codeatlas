import { create } from 'zustand';

import type { AnalysisDocument } from '../types/analysis';

interface AnalysisState {
  document: AnalysisDocument | null;
  loadError: string | null;
  setDocument: (doc: AnalysisDocument) => void;
  setLoadError: (message: string) => void;
  clear: () => void;
}

export const useAnalysisStore = create<AnalysisState>((set) => ({
  document: null,
  loadError: null,
  setDocument: (doc) => {
    set({ document: doc, loadError: null });
  },
  setLoadError: (message) => {
    set({ loadError: message });
  },
  clear: () => {
    set({ document: null, loadError: null });
  },
}));
