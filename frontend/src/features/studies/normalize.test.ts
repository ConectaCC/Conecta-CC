import { expect, it } from 'vitest';
import { normalizeSearch } from './normalize';

it('ignora acentos, maiúsculas e espaços nas pontas', () => {
  expect(normalizeSearch('  Computação GRÁFICA ')).toBe('computacao grafica');
  expect(normalizeSearch('Cálculo')).toBe(normalizeSearch('calculo'));
});
