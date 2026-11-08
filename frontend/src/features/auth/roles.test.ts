import { expect, it } from 'vitest';
import { canModerate, isAdmin } from './roles';

it('só MODERADOR e ADMINISTRADOR veem a Moderação', () => {
  expect([canModerate('ALUNO'), canModerate('MODERADOR'), canModerate('ADMINISTRADOR')]).toEqual([false, true, true]);
});

it('só o ADMINISTRADOR vê o painel', () => {
  expect([isAdmin('ALUNO'), isAdmin('MODERADOR'), isAdmin('ADMINISTRADOR')]).toEqual([false, false, true]);
});
