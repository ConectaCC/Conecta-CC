import { screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { expect, it, vi } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderWithProviders } from '@/test/render';
import { DecisionDialog, type DecisionKind } from './DecisionDialog';
import type { QueueItem } from './hooks';

const queued = { kind: 'material', item: { id: 5, title: 'Lista 1', discipline: { id: 4 } } } as unknown as QueueItem;

function renderDialog(kind: DecisionKind) {
  const props = { onCancel: vi.fn(), onDone: vi.fn(), onConflict: vi.fn() };
  renderWithProviders(<DecisionDialog kind={kind} queued={queued} {...props} />);
  return props;
}

const reasonField = () => screen.getByLabelText(/^Motivo da rejeição/);

it('rejeitar exige o motivo, sem chamar a API', async () => {
  const post = mockApi('POST');
  renderDialog('reject');

  await userEvent.type(reasonField(), '   ');
  await userEvent.click(screen.getByRole('button', { name: 'Rejeitar conteúdo' }));

  expect(await screen.findByText('Informe o motivo da rejeição.')).toBeInTheDocument();
  expect(post).not.toHaveBeenCalled();
});

it('rejeita com o motivo e avisa quem abriu', async () => {
  const post = mockApi('POST', reply(200, {}));
  const props = renderDialog('reject');

  await userEvent.type(reasonField(), 'Link fora do tema.');
  await userEvent.click(screen.getByRole('button', { name: 'Rejeitar conteúdo' }));

  await vi.waitFor(() => expect(props.onDone).toHaveBeenCalledWith('reject'));
  expect(post).toHaveBeenCalledWith('/moderation/materials/{material}/reject', { params: { path: { material: 5 } }, body: { reason: 'Link fora do tema.' } });
});

it('mostra no campo o erro de motivo vindo da API', async () => {
  mockApi('POST', reply(422, { message: 'x', errors: { reason: ['Use no máximo 500 caracteres.'] } }));
  renderDialog('reject');

  await userEvent.type(reasonField(), 'Motivo');
  await userEvent.click(screen.getByRole('button', { name: 'Rejeitar conteúdo' }));

  expect(await screen.findByText('Use no máximo 500 caracteres.')).toBeInTheDocument();
});

it('decisão que chegou tarde fecha e informa o código do conflito', async () => {
  mockApi('POST', reply(409, { message: 'Já decidido.', code: 'already_decided' }));
  const props = renderDialog('approve');

  await userEvent.click(screen.getByRole('button', { name: 'Aprovar conteúdo' }));

  await vi.waitFor(() => expect(props.onConflict).toHaveBeenCalledWith('already_decided'));
});

it('link repetido na alteração: mostra a mensagem e mantém o diálogo aberto', async () => {
  mockApi('POST', reply(409, { message: 'Esse link já foi compartilhado nesta disciplina.', code: 'link_taken' }));
  const props = renderDialog('approve');

  await userEvent.click(screen.getByRole('button', { name: 'Aprovar conteúdo' }));

  expect(await screen.findByText('Esse link já foi compartilhado nesta disciplina.')).toBeInTheDocument();
  expect(props.onConflict).not.toHaveBeenCalled();
});

it('falha inesperada: avisa para tentar de novo', async () => {
  mockApi('POST', reply(500, {}));
  renderDialog('approve');

  await userEvent.click(screen.getByRole('button', { name: 'Aprovar conteúdo' }));

  expect(await screen.findByText('Não foi possível aprovar o conteúdo. Tente novamente.')).toBeInTheDocument();
});
