import { screen } from '@testing-library/react';
import { expect, it } from 'vitest';
import { renderWithProviders } from '@/test/render';
import { FeedEntry } from './FeedEntry';
import type { FeedItem } from './hooks';

const material = (overrides: Partial<FeedItem> = {}): FeedItem => ({
  kind: 'material',
  id: 7,
  code: 'MAT-00007',
  title: 'Lista de grafos',
  url: 'https://site.com/lista',
  published_at: new Date().toISOString(),
  discipline: { name: 'Algoritmos I', slug: 'algoritmos-i' },
  opportunity_type: null,
  author_name: 'Ana Silva',
  author_code: 'f4610df616ff4220',
  is_mine: false,
  ...overrides,
});

it('o material identificado leva ao perfil público de quem enviou', () => {
  renderWithProviders(<FeedEntry item={material()} />);

  expect(screen.getByRole('link', { name: 'Ana Silva' })).toHaveAttribute('href', '/estudantes/f4610df616ff4220');
  expect(screen.getByText('AS')).toBeInTheDocument();
  expect(screen.getByRole('link', { name: '“Lista de grafos”' })).toHaveAttribute('href', '/estudos/algoritmos-i#material-7');
  expect(screen.getByRole('link', { name: 'Algoritmos I' })).toHaveAttribute('href', '/estudos/algoritmos-i');
  expect(screen.getByText('Material')).toBeInTheDocument();
});

it('o próprio material leva ao Perfil', () => {
  renderWithProviders(<FeedEntry item={material({ is_mine: true })} />);

  expect(screen.getByRole('link', { name: 'Ana Silva' })).toHaveAttribute('href', '/perfil');
});

it('o material anônimo aparece como Comunidade, sem nome nem link de perfil', () => {
  renderWithProviders(<FeedEntry item={material({ author_name: null, author_code: null })} />);

  expect(screen.getByText('Comunidade')).toBeInTheDocument();
  expect(screen.queryByRole('link', { name: /Comunidade/ })).not.toBeInTheDocument();
  expect(document.querySelector('a[href^="/estudantes"]')).toBeNull();
});

it('a oportunidade nunca mostra quem enviou, mesmo se o nome viesse da API', () => {
  renderWithProviders(
    <FeedEntry item={material({ kind: 'opportunity', discipline: null, opportunity_type: 'ESTAGIO', author_name: 'Ana Silva', author_code: 'f4610df616ff4220' })} />,
  );

  expect(screen.queryByText('Ana Silva')).not.toBeInTheDocument();
  expect(screen.queryByText('AS')).not.toBeInTheDocument();
  expect(document.querySelector('a[href^="/estudantes"]')).toBeNull();
  expect(screen.getByText('Estágio')).toBeInTheDocument();
  expect(screen.getByRole('link', { name: '“Lista de grafos”' })).toHaveAttribute('href', '/oportunidade#oportunidade-7');
  expect(screen.getByText('Oportunidade')).toBeInTheDocument();
});

it('mostra o tipo como veio quando não tem rótulo', () => {
  renderWithProviders(<FeedEntry item={material({ kind: 'opportunity', discipline: null, opportunity_type: 'NOVO_TIPO' })} />);

  expect(screen.getByText('NOVO_TIPO')).toBeInTheDocument();
});

it('mostra há quanto tempo foi publicado, e nada quando não há data', () => {
  const { unmount } = renderWithProviders(<FeedEntry item={material({ published_at: new Date(Date.now() - 2 * 86_400_000).toISOString() })} />);
  expect(screen.getByText('anteontem')).toBeInTheDocument();
  unmount();

  renderWithProviders(<FeedEntry item={material({ published_at: null })} />);
  expect(screen.queryByText('•')).not.toBeInTheDocument();
});
