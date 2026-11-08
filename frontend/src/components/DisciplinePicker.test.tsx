import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { expect, it, vi } from 'vitest';
import { DisciplinePicker } from './DisciplinePicker';

const OPTIONS = [
  { id: 1, name: 'Algoritmos I' },
  { id: 2, name: 'Cálculo I' },
  { id: 3, name: 'Circuitos Digitais' },
];

function renderPicker(value: number[] = []) {
  const onChange = vi.fn();
  Element.prototype.scrollIntoView = vi.fn();
  render(<DisciplinePicker id="disciplinas" options={OPTIONS} value={value} onChange={onChange} />);
  return { onChange, input: screen.getByRole('combobox') };
}

it('a seta para baixo abre a lista fechada sem mover a seleção', async () => {
  const { input } = renderPicker();
  await userEvent.click(input);
  await userEvent.keyboard('{Escape}');
  expect(input).toHaveAttribute('aria-expanded', 'false');

  await userEvent.keyboard('{ArrowDown}');

  expect(input).toHaveAttribute('aria-expanded', 'true');
  expect(screen.getByRole('option', { name: /Algoritmos I/ })).toHaveAttribute('aria-selected', 'true');
});

it('com a lista aberta, as setas andam pelas opções e voltam ao início', async () => {
  const { input } = renderPicker();
  await userEvent.click(input);

  await userEvent.keyboard('{ArrowDown}');
  expect(screen.getByRole('option', { name: /Cálculo I/ })).toHaveAttribute('aria-selected', 'true');
  await userEvent.keyboard('{ArrowUp}{ArrowUp}');
  expect(screen.getByRole('option', { name: /Circuitos Digitais/ })).toHaveAttribute('aria-selected', 'true');
});

it('busca sem acentos e escolhe com Enter', async () => {
  const { input, onChange } = renderPicker([1]);

  await userEvent.type(input, 'calculo');
  await userEvent.keyboard('{Enter}');

  expect(onChange).toHaveBeenCalledWith([1, 2]);
});

it('não oferece de novo a disciplina já escolhida', async () => {
  const { input } = renderPicker([1]);

  await userEvent.click(input);

  expect(screen.queryByRole('option', { name: /Algoritmos I/ })).not.toBeInTheDocument();
});
