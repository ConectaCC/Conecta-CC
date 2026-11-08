import { render, screen } from '@testing-library/react';
import { expect, it } from 'vitest';
import { StatusTag } from './StatusTag';

it.each([
  ['PENDENTE', 'Em análise'],
  ['APROVADO', 'Aprovado'],
  ['REJEITADO', 'Rejeitado'],
  ['EXCLUIDO', 'Excluído'],
  ['CANCELADO', 'Cancelado'],
  ['ENCERRADO', 'Encerrado'],
  ['ALTERACAO', 'Alteração'],
  ['DESCONHECIDO', 'Em análise'],
])('%s aparece como "%s"', (status, label) => {
  render(<StatusTag status={status} />);
  expect(screen.getByText(label)).toBeInTheDocument();
});

it('aceita um rótulo próprio', () => {
  render(<StatusTag status="APROVADO" label="Aprovada" />);
  expect(screen.getByText('Aprovada')).toBeInTheDocument();
});
