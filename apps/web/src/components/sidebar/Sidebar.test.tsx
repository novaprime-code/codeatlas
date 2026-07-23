import { beforeEach, describe, expect, it } from 'vitest';
import { fireEvent, render, screen } from '@testing-library/react';

import { Sidebar } from './Sidebar';
import { parseAnalysisDocument } from '../../lib/analysis-loader';
import { useAnalysisStore } from '../../stores/analysisStore';
import { useGraphStore } from '../../stores/graphStore';
import fixtureRaw from '../../fixtures/analysis.json?raw';

const doc = parseAnalysisDocument(fixtureRaw);

describe('Sidebar', () => {
  beforeEach(() => {
    useAnalysisStore.setState({ document: doc, loadError: null });
    useGraphStore.setState({ selectedNodeId: null });
  });

  it('groups nodes by type with counts', () => {
    render(<Sidebar />);

    expect(screen.getByText('routes')).toBeInTheDocument();
    expect(screen.getByText('4')).toBeInTheDocument();
  });

  it('lists every route label', () => {
    render(<Sidebar />);

    expect(screen.getByText('GET /users')).toBeInTheDocument();
    expect(screen.getByText('GET /api/users')).toBeInTheDocument();
  });

  it('filters nodes by search query', () => {
    render(<Sidebar />);

    fireEvent.change(screen.getByLabelText('Search nodes'), { target: { value: 'api' } });

    expect(screen.getByText('GET /api/users')).toBeInTheDocument();
    expect(screen.queryByText('POST /users')).not.toBeInTheDocument();
  });

  it('selects a node on click', () => {
    render(<Sidebar />);

    fireEvent.click(screen.getByText('GET /users'));

    expect(useGraphStore.getState().selectedNodeId).toBe('route::get::/users');
  });

  it('collapses a section on header click', () => {
    render(<Sidebar />);

    fireEvent.click(screen.getByText('routes'));

    expect(screen.queryByText('GET /users')).not.toBeInTheDocument();
  });

  it('prompts to load when no document is present', () => {
    useAnalysisStore.setState({ document: null });
    render(<Sidebar />);

    expect(screen.getByText('Load an analysis to begin.')).toBeInTheDocument();
  });
});
