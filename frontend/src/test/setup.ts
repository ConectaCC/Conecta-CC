import '@testing-library/jest-dom/vitest';
import { cleanup } from '@testing-library/react';
import { afterEach, vi } from 'vitest';

// datas e horas dos testes no fuso do câmpus, em qualquer processo (Vitest ou StrykerJS)
vi.stubEnv('TZ', 'America/Sao_Paulo');

// o jsdom não tem ResizeObserver, que os componentes do Radix usam para medir
globalThis.ResizeObserver = class {
  observe() {}
  unobserve() {}
  disconnect() {}
};

afterEach(() => {
  cleanup();
  vi.restoreAllMocks();
  vi.useRealTimers();
});
