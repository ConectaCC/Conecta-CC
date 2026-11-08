import { render, screen } from '@testing-library/react';
import { expect, it } from 'vitest';
import type { Opportunity } from './hooks';
import { OpportunityCard } from './OpportunityCard';

const opportunity = (overrides: Partial<Opportunity> = {}): Opportunity => ({
  id: 3,
  code: 'OPO-00003',
  type: 'ESTAGIO',
  title: 'Estágio em web',
  description: 'Vaga para o 3º semestre.',
  url: 'https://www.empresa.com/vaga',
  modality: 'HIBRIDO',
  deadline: '2026-12-01',
  published_at: new Date().toISOString(),
  ...overrides,
});

it('mostra tipo, modalidade, prazo e o link externo seguro, sem autoria', () => {
  render(<OpportunityCard item={opportunity()} />);

  expect(screen.getByRole('article')).toHaveAttribute('id', 'oportunidade-3');
  expect(screen.getByText('Estágio')).toBeInTheDocument();
  expect(screen.getByText('Híbrido')).toBeInTheDocument();
  expect(screen.getByText('Inscrições até 01/12/2026')).toBeInTheDocument();
  expect(screen.getByText('Publicada agora')).toBeInTheDocument();
  const link = screen.getByRole('link', { name: /Acessar oportunidade/ });
  expect(link).toHaveAttribute('href', 'https://www.empresa.com/vaga');
  expect(link).toHaveAttribute('target', '_blank');
  expect(link).toHaveAttribute('rel', 'noopener noreferrer');
  expect(link).toHaveTextContent('(abre em nova aba, empresa.com)');
  expect(screen.queryByText(/Compartilhad|Enviad|por /)).not.toBeInTheDocument();
});

it('sem prazo e sem data de publicação, não mostra as linhas', () => {
  render(<OpportunityCard item={opportunity({ deadline: null, published_at: null })} />);

  expect(screen.queryByText(/Inscrições até/)).not.toBeInTheDocument();
  expect(screen.queryByText(/Publicada/)).not.toBeInTheDocument();
});

it('mostra tipo e modalidade como vieram quando não têm rótulo', () => {
  render(<OpportunityCard item={opportunity({ type: 'NOVO', modality: 'OUTRA' })} />);

  expect(screen.getByText('NOVO')).toBeInTheDocument();
  expect(screen.getByText('OUTRA')).toBeInTheDocument();
});
