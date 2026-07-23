import type { AnalysisDocument } from '../types/analysis';

const SUPPORTED_MAJOR = 1;

/**
 * Parse and validate raw JSON text into an AnalysisDocument.
 *
 * Per JSON_SCHEMA.md the UI must check the version and handle unknown
 * fields gracefully — so validation asserts only the structural minimum
 * (schema marker, compatible major version, graph arrays) and passes
 * everything else through.
 */
export function parseAnalysisDocument(raw: string): AnalysisDocument {
  let data: unknown;

  try {
    data = JSON.parse(raw);
  } catch {
    throw new Error('The file is not valid JSON.');
  }

  if (typeof data !== 'object' || data === null) {
    throw new Error('The document root must be a JSON object.');
  }

  const doc = data as Record<string, unknown>;

  if (typeof doc['$schema'] !== 'string' || !doc['$schema'].includes('codeatlas')) {
    throw new Error('Not a CodeAtlas analysis document (missing $schema).');
  }

  if (typeof doc['version'] !== 'string') {
    throw new Error('The document is missing a schema version.');
  }

  const major = Number.parseInt(doc['version'].split('.')[0] ?? '', 10);
  if (Number.isNaN(major) || major > SUPPORTED_MAJOR) {
    throw new Error(
      `Unsupported schema version ${doc['version']} (this UI supports up to ${String(SUPPORTED_MAJOR)}.x).`,
    );
  }

  const graph = doc['graph'];
  if (
    typeof graph !== 'object' ||
    graph === null ||
    !Array.isArray((graph as Record<string, unknown>)['nodes']) ||
    !Array.isArray((graph as Record<string, unknown>)['edges'])
  ) {
    throw new Error('The document has no graph.nodes / graph.edges arrays.');
  }

  return data as AnalysisDocument;
}

export async function readAnalysisFile(file: File): Promise<AnalysisDocument> {
  const text = await file.text();

  return parseAnalysisDocument(text);
}
