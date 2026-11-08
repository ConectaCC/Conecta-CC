import { expect, it, vi } from 'vitest';
import { applyFieldErrors } from './applyFieldErrors';

it('põe a primeira mensagem em cada campo conhecido, foca só o primeiro e devolve o resto', () => {
  const setError = vi.fn();

  const rest = applyFieldErrors(setError, { email: ['E-mail inválido.', 'outra'], password: ['Curta.'], terms_version: ['Termos.'] }, ['email', 'password']);

  expect(setError.mock.calls).toEqual([
    ['email', { type: 'server', message: 'E-mail inválido.' }, { shouldFocus: true }],
    ['password', { type: 'server', message: 'Curta.' }, { shouldFocus: false }],
  ]);
  expect(rest).toEqual({ terms_version: ['Termos.'] });
});
