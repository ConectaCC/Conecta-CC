import { screen } from '@testing-library/react';
import { expect, it, vi } from 'vitest';
import * as client from '@/lib/api/client';
import { renderWithProviders } from '@/test/render';
import { MyContentsPage } from './MyContentsPage';

it('destaca só a aba do tipo de conteúdo escolhido', () => {
  // a lista fica carregando: aqui só importam as abas
  vi.spyOn(client.api, 'GET').mockReturnValue(new Promise(() => {}));
  renderWithProviders(<MyContentsPage />, { route: '/perfil/meus-conteudos?tipo=oportunidades' });

  const materials = screen.getByRole('button', { name: 'Materiais' });
  const opportunities = screen.getByRole('button', { name: 'Oportunidades' });
  expect(opportunities).toHaveAttribute('aria-pressed', 'true');
  expect(opportunities).toHaveClass('text-action');
  expect(materials).toHaveAttribute('aria-pressed', 'false');
  expect(materials).not.toHaveClass('text-action');
});
