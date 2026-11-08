import { screen } from '@testing-library/react';
import { expect, it } from 'vitest';
import { renderWithProviders } from '@/test/render';
import type { Material } from './hooks';
import { MaterialCard } from './MaterialCard';

const material = (overrides: Partial<Material> = {}): Material => ({
  id: 9,
  code: 'MAT-00009',
  title: 'Resumo de cálculo',
  description: 'Com exercícios.',
  url: 'https://drive.google.com/file/d/1',
  author_name: 'Ana Silva',
  author_code: 'f4610df616ff4220',
  published_at: null,
  is_mine: false,
  ...overrides,
});

const discipline = { id: 1, name: 'Cálculo I' };

it('o material identificado mostra quem compartilhou, com link para o perfil público', () => {
  renderWithProviders(<MaterialCard material={material()} discipline={discipline} />);

  expect(screen.getByText(/Compartilhado por/)).toBeInTheDocument();
  expect(screen.getByRole('link', { name: 'Ana Silva' })).toHaveAttribute('href', '/estudantes/f4610df616ff4220');
  expect(screen.queryByRole('button', { name: /Ações do material/ })).not.toBeInTheDocument();
});

it('o material anônimo aparece como da comunidade, sem nome nem link', () => {
  renderWithProviders(<MaterialCard material={material({ author_name: null, author_code: null })} discipline={discipline} />);

  expect(screen.getByText('Compartilhado pela comunidade')).toBeInTheDocument();
  expect(document.querySelector('a[href^="/estudantes"]')).toBeNull();
});

it('o próprio material leva ao Perfil e oferece as ações', () => {
  renderWithProviders(<MaterialCard material={material({ is_mine: true })} discipline={discipline} />);

  expect(screen.getByRole('link', { name: 'Ana Silva' })).toHaveAttribute('href', '/perfil');
  expect(screen.getByRole('button', { name: 'Ações do material Resumo de cálculo' })).toBeInTheDocument();
});

it('abre o link em nova aba, de forma segura', () => {
  renderWithProviders(<MaterialCard material={material()} discipline={discipline} />);

  const link = screen.getByRole('link', { name: /Acessar material/ });
  expect(link).toHaveAttribute('href', 'https://drive.google.com/file/d/1');
  expect(link).toHaveAttribute('target', '_blank');
  expect(link).toHaveAttribute('rel', 'noopener noreferrer');
});
