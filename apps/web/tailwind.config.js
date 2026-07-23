/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        atlas: {
          'bg-primary': 'var(--atlas-bg-primary)',
          'bg-secondary': 'var(--atlas-bg-secondary)',
          'bg-tertiary': 'var(--atlas-bg-tertiary)',
          'bg-elevated': 'var(--atlas-bg-elevated)',
          border: 'var(--atlas-border)',
          'border-active': 'var(--atlas-border-active)',
          'text-primary': 'var(--atlas-text-primary)',
          'text-secondary': 'var(--atlas-text-secondary)',
          'text-muted': 'var(--atlas-text-muted)',
          accent: 'var(--atlas-accent)',
          'accent-hover': 'var(--atlas-accent-hover)',
          'accent-subtle': 'var(--atlas-accent-subtle)',
          success: 'var(--atlas-success)',
          warning: 'var(--atlas-warning)',
          error: 'var(--atlas-error)',
          info: 'var(--atlas-info)',
        },
        node: {
          route: '#3b82f6',
          controller: '#8b5cf6',
          middleware: '#f59e0b',
          service: '#22c55e',
          repository: '#14b8a6',
          model: '#ef4444',
          event: '#f97316',
          listener: '#a855f7',
          job: '#06b6d4',
          notification: '#ec4899',
          policy: '#84cc16',
          command: '#64748b',
        },
      },
      fontFamily: {
        mono: ['JetBrains Mono', 'Fira Code', 'Cascadia Code', 'monospace'],
        sans: ['Inter', '-apple-system', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
};
