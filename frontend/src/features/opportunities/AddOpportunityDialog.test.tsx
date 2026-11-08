import { screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { expect, it, vi } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderWithProviders } from '@/test/render';
import { AddOpportunityDialog } from './AddOpportunityDialog';

const send = () => userEvent.click(screen.getByRole('button', { name: 'Enviar para análise' }));

function renderDialog() {
  renderWithProviders(<AddOpportunityDialog open onOpenChange={vi.fn()} backLabel="Voltar para Oportunidade" />);
}

async function fillRequired() {
  await userEvent.selectOptions(screen.getByLabelText('Tipo *'), 'ESTAGIO');
  await userEvent.type(screen.getByLabelText('Título *'), 'Estágio em web');
  await userEvent.type(screen.getByLabelText('Descrição curta *'), 'Vaga para o 3º semestre.');
  await userEvent.type(screen.getByLabelText('Link *'), 'empresa.com/vaga');
  await userEvent.click(screen.getByLabelText('Remoto'));
}

it('exige tipo, título, descrição, link e modalidade, sem chamar a API', async () => {
  const post = mockApi('POST');
  renderDialog();

  await send();

  for (const message of ['Escolha o tipo da oportunidade.', 'Informe um título para a oportunidade.', 'Descreva a oportunidade em poucas palavras.', 'Informe o link da oportunidade.', 'Escolha a modalidade.']) {
    expect(await screen.findByText(message)).toBeInTheDocument();
  }
  expect(post).not.toHaveBeenCalled();
});

it('envia sem prazo como null e confirma a indicação', async () => {
  const post = mockApi('POST', reply(201, { code: 'OPO-00001' }));
  renderDialog();

  await fillRequired();
  await send();

  expect(await screen.findByText(/Você receberá uma notificação quando sua indicação for analisada/)).toBeInTheDocument();
  expect(screen.getByRole('button', { name: 'Voltar para Oportunidade' })).toBeInTheDocument();
  expect(post).toHaveBeenCalledWith('/opportunities', {
    body: { type: 'ESTAGIO', title: 'Estágio em web', description: 'Vaga para o 3º semestre.', url: 'empresa.com/vaga', modality: 'REMOTO', deadline: null },
  });
});

it('envia o prazo escolhido e não aceita data antes de hoje no campo', async () => {
  vi.useFakeTimers({ toFake: ['Date'] });
  vi.setSystemTime(new Date(2026, 10, 8, 12));
  const post = mockApi('POST', reply(201, { code: 'OPO-00001' }));
  renderDialog();

  expect(screen.getByLabelText('Inscrições até')).toHaveAttribute('min', '2026-11-08');
  await fillRequired();
  await userEvent.type(screen.getByLabelText('Inscrições até'), '2026-12-01');
  await send();

  await screen.findByText(/Você receberá uma notificação/);
  expect(post.mock.calls[0]?.[1]).toMatchObject({ body: { deadline: '2026-12-01' } });
});

it('conta os caracteres da descrição e limita a 300', async () => {
  renderDialog();

  await userEvent.type(screen.getByLabelText('Descrição curta *'), 'abc');

  expect(screen.getByText('3/300')).toBeInTheDocument();
  expect(screen.getByLabelText('Descrição curta *')).toHaveAttribute('maxLength', '300');
  expect(screen.getByLabelText('Título *')).toHaveAttribute('maxLength', '200');
});

it('mostra no campo o erro de link repetido vindo da API', async () => {
  mockApi('POST', reply(422, { message: 'x', errors: { url: ['Essa oportunidade já foi compartilhada.'] } }));
  renderDialog();

  await fillRequired();
  await send();

  expect(await screen.findByText('Essa oportunidade já foi compartilhada.')).toBeInTheDocument();
});

it('erro sem campo conhecido vira o aviso geral', async () => {
  mockApi('POST', reply(422, { message: 'x', errors: { outro: ['?'] } }));
  renderDialog();

  await fillRequired();
  await send();

  expect(await screen.findByText('Não foi possível enviar a oportunidade. Tente novamente.')).toBeInTheDocument();
});

it('pede confirmação antes de descartar o que foi digitado', async () => {
  renderDialog();

  await userEvent.type(screen.getByLabelText('Título *'), 'Estágio');
  await userEvent.click(screen.getByRole('button', { name: 'Cancelar' }));

  expect(await screen.findByText('As informações desta oportunidade ainda não foram enviadas.')).toBeInTheDocument();
});
