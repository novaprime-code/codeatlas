import { create } from 'zustand';

interface UiState {
  theme: 'dark' | 'light';
  sidebarOpen: boolean;
  inspectorOpen: boolean;
  consoleOpen: boolean;
  toggleTheme: () => void;
  toggleSidebar: () => void;
  toggleInspector: () => void;
  toggleConsole: () => void;
}

export const useUiStore = create<UiState>((set) => ({
  theme: 'dark',
  sidebarOpen: true,
  inspectorOpen: true,
  consoleOpen: false,
  toggleTheme: () => {
    set((s) => ({ theme: s.theme === 'dark' ? 'light' : 'dark' }));
  },
  toggleSidebar: () => {
    set((s) => ({ sidebarOpen: !s.sidebarOpen }));
  },
  toggleInspector: () => {
    set((s) => ({ inspectorOpen: !s.inspectorOpen }));
  },
  toggleConsole: () => {
    set((s) => ({ consoleOpen: !s.consoleOpen }));
  },
}));
