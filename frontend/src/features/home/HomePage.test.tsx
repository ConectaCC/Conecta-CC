import { screen } from '@testing-library/react';
import { expect, it } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderWithProviders } from '@/test/render';
import { HomePage } from './HomePage';

const me = (profileComplete: boolean) => ({
  id: 1,
  name: 'Ana Maria Silva',
  email: 'ana@academico.ifsul.edu.br',
  display_name: 'Ana Silva',
  initials: 'AS',
  role: 'ALUNO',
  profile_complete: profileComplete,
});

// o tamanho real é conferido nos testes de ponta a ponta (e2e/home.spec.ts); aqui, a regra de quando o bloco fica lado a lado
const hero = () => screen.getByRole('heading', { name: /Conecte-se/ }).closest('section');

it('com o perfil incompleto, mostra o card e deixa o texto e os atalhos em coluna', async () => {
  mockApi('GET', reply(200, me(false)));
  renderWithProviders(<HomePage />);

  expect(await screen.findByText('Olá, Ana!')).toBeInTheDocument();
  expect(screen.getByRole('heading', { name: 'Complete seu perfil' })).toBeInTheDocument();
  expect(hero()).not.toHaveClass('xl:flex-row');
});

it('com o perfil completo, esconde o card e põe o texto e os atalhos lado a lado nas telas largas', async () => {
  mockApi('GET', reply(200, me(true)));
  renderWithProviders(<HomePage />);

  expect(await screen.findByText('Olá, Ana!')).toBeInTheDocument();
  expect(screen.queryByRole('heading', { name: 'Complete seu perfil' })).not.toBeInTheDocument();
  expect(hero()).toHaveClass('xl:flex-row', 'xl:items-center');
});
