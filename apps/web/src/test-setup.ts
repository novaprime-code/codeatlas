import '@testing-library/jest-dom';

// jsdom does not implement ResizeObserver; React Flow requires it.
class ResizeObserverStub {
  observe(): void {}
  unobserve(): void {}
  disconnect(): void {}
}

// eslint-disable-next-line @typescript-eslint/no-unnecessary-condition -- TS lib types say ResizeObserver always exists; jsdom does not provide it at runtime
globalThis.ResizeObserver ??= ResizeObserverStub;

// React Flow also queries DOMMatrixReadOnly for zoom math.
if (typeof globalThis.DOMMatrixReadOnly === 'undefined') {
  // @ts-expect-error minimal stub sufficient for jsdom rendering
  globalThis.DOMMatrixReadOnly = class {
    m22 = 1;
  };
}
