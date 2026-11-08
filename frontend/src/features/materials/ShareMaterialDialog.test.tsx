import { screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { expect, it, vi } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderWithProviders } from '@/test/render';
import { ShareMaterialDialog } from './ShareMaterialDialog';

const discipline = { id: 4, name: 'Algoritmos I' };
const send = () => userEvent.click(screen.getByRole('button', { name: 'Enviar para análise' }));

it('exige título e link, sem chamar a API', async () => {
  const post = mockApi('POST');
  renderWithProviders(<ShareMaterialDialog discipline={discipline} open onOpenChange={vi.fn()} />);

  await userEvent.type(screen.getByLabelText('Título *'), '   ');
  await send();

  expect(await screen.findByText('Informe um título para o material.')).toBeInTheDocument();
  expect(screen.getByText('Informe o link do material.')).toBeInTheDocument();
  expect(post).not.toHaveBeenCalled();
});

it('envia identificado por padrão, com descrição vazia como null, e confirma o envio', async () => {
  const post = mockApi('POST', reply(201, { code: 'MAT-00001' }));
  renderWithProviders(<ShareMaterialDialog discipline={discipline} open onOpenChange={vi.fn()} />);

  await userEvent.type(screen.getByLabelText('Título *'), 'Lista 1');
  await userEvent.type(screen.getByLabelText('Descrição'), '   ');
  await userEvent.type(screen.getByLabelText('Link *'), 'site.com/lista');
  await send();

  expect(await screen.findByText('Material enviado para análise')).toBeInTheDocument();
  expect(post).toHaveBeenCalledWith('/disciplines/{discipline}/materials', {
    params: { path: { discipline: 4 } },
    body: { title: 'Lista 1', description: null, url: 'site.com/lista', anonymous: false },
  });
});

it('envia anônimo quando marcado', async () => {
  const post = mockApi('POST', reply(201, { code: 'MAT-00001' }));
  renderWithProviders(<ShareMaterialDialog discipline={discipline} open onOpenChange={vi.fn()} />);

  await userEvent.type(screen.getByLabelText('Título *'), 'Lista 1');
  await userEvent.type(screen.getByLabelText('Link *'), 'site.com/lista');
  await userEvent.click(screen.getByLabelText('Compartilhar anonimamente'));
  await send();

  await screen.findByText('Material enviado para análise');
  expect(post.mock.calls[0]?.[1]).toMatchObject({ body: { anonymous: true } });
});

it('mostra no campo o erro de link repetido vindo da API', async () => {
  mockApi('POST', reply(422, { message: 'x', errors: { url: ['Esse link já foi compartilhado nesta disciplina.'] } }));
  renderWithProviders(<ShareMaterialDialog discipline={discipline} open onOpenChange={vi.fn()} />);

  await userEvent.type(screen.getByLabelText('Título *'), 'Lista 1');
  await userEvent.type(screen.getByLabelText('Link *'), 'site.com/lista');
  await send();

  expect(await screen.findByText('Esse link já foi compartilhado nesta disciplina.')).toBeInTheDocument();
});

it('limite de envios: mostra a mensagem da API', async () => {
  mockApi('POST', reply(429, { message: 'Muitas tentativas. Tente novamente em 60 segundos.' }));
  renderWithProviders(<ShareMaterialDialog discipline={discipline} open onOpenChange={vi.fn()} />);

  await userEvent.type(screen.getByLabelText('Título *'), 'Lista 1');
  await userEvent.type(screen.getByLabelText('Link *'), 'site.com/lista');
  await send();

  expect(await screen.findByText('Muitas tentativas. Tente novamente em 60 segundos.')).toBeInTheDocument();
});

it('pede confirmação antes de descartar o que foi digitado', async () => {
  const onOpenChange = vi.fn();
  renderWithProviders(<ShareMaterialDialog discipline={discipline} open onOpenChange={onOpenChange} />);

  await userEvent.type(screen.getByLabelText('Título *'), 'Lista 1');
  await userEvent.click(screen.getByRole('button', { name: 'Cancelar' }));

  expect(await screen.findByText('As informações deste material ainda não foram enviadas.')).toBeInTheDocument();
  expect(onOpenChange).not.toHaveBeenCalled();
});

it('na edição, envia a alteração do material e não oferece anonimato', async () => {
  const put = mockApi('PUT', reply(201, {}));
  renderWithProviders(
    <ShareMaterialDialog discipline={discipline} open onOpenChange={vi.fn()} editing={{ id: 9, title: 'Lista 1', description: null, url: 'https://site.com/lista' }} />,
  );

  expect(screen.queryByLabelText('Compartilhar anonimamente')).not.toBeInTheDocument();
  await userEvent.clear(screen.getByLabelText('Título *'));
  await userEvent.type(screen.getByLabelText('Título *'), 'Lista 1 revisada');
  await send();

  expect(await screen.findByText('Alteração enviada para análise')).toBeInTheDocument();
  expect(put).toHaveBeenCalledWith('/materials/{material}', {
    params: { path: { material: 9 } },
    body: { title: 'Lista 1 revisada', description: null, url: 'https://site.com/lista', anonymous: false },
  });
});
