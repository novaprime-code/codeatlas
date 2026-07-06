/**
 * TypeScript mirror of the CodeAtlas JSON schema (JSON_SCHEMA.md).
 * The UI only ever consumes this shape — never PHP.
 */

export type NodeType =
  | 'route'
  | 'controller'
  | 'controller_method'
  | 'middleware'
  | 'middleware_group'
  | 'service'
  | 'repository'
  | 'model'
  | 'model_relationship'
  | 'event'
  | 'listener'
  | 'job'
  | 'notification'
  | 'policy'
  | 'policy_method'
  | 'command'
  | 'schedule_entry'
  | 'migration'
  | 'factory'
  | 'seeder'
  | 'provider'
  | 'config'
  | 'view';

export type EdgeType =
  | 'routes_to'
  | 'calls'
  | 'depends_on'
  | 'extends'
  | 'implements'
  | 'uses_trait'
  | 'uses_middleware'
  | 'has_relationship'
  | 'dispatches'
  | 'listens_to'
  | 'queues'
  | 'notifies'
  | 'authorizes'
  | 'schedules'
  | 'migrates';

export interface FileReference {
  path: string;
  line_start: number;
  line_end: number;
}

export interface AnalysisNode {
  id: string;
  type: NodeType;
  label: string;
  group?: string;
  file?: FileReference;
  metadata: Record<string, unknown>;
  tags?: string[];
}

export interface AnalysisEdge {
  id: string;
  source: string;
  target: string;
  type: EdgeType;
  label?: string;
  metadata?: Record<string, unknown>;
}

export interface AnalysisError {
  analyzer: string;
  severity: 'error' | 'warning' | 'info';
  message: string;
  file: string | null;
  line: number | null;
  exception?: string;
}

export interface AnalysisDocument {
  $schema: string;
  version: string;
  project: {
    name: string;
    path: string;
    framework: string;
    framework_version: string;
    php_version: string;
  };
  analysis: {
    timestamp: string;
    duration_ms: number;
    analyzers: string[];
  };
  graph: {
    nodes: AnalysisNode[];
    edges: AnalysisEdge[];
  };
  results: Record<string, unknown>;
  errors: AnalysisError[];
}
