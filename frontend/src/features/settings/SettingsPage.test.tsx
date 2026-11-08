import { act, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { expect, it } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderWithDataRouter } from '@/test/render';
import { SettingsPage } from './SettingsPage';

const ME = { id: 1, name: 'Ana', email: 'ana@academico.ifsul.edu.br', display_name: 'Ana', initials: 'A', role: 'ALUNO', profile_complete: true };

function renderSettings() {
  const { router } = renderWithDataRouter(<SettingsPage />, { path: '/configuracoes', query: { me: ME }, routes: [{ path: '/home', element: <p>Página inicial</p> }] });
  return {
    current: screen.getByLabelText(/^Senha atual/),
    password: screen.getByLabelText(/^Nova senha/),
    confirmation: screen.getByLabelText(/^Confirmar nova senha/),
    save: () => userEvent.click(screen.getByRole('button', { name: 'Salvar nova senha' })),
    router,
  };
}

it('mostra o e-mail só para leitura', () => {
  renderSettings();

  expect(screen.getByText('ana@academico.ifsul.edu.br')).toBeInTheDocument();
  expect(screen.queryByRole('textbox', { name: /e-mail/i })).not.toBeInTheDocument();
});

it('valida os três campos sem chamar a API', async () => {
  const put = mockApi('PUT');
  const form = renderSettings();

  await form.save();

  expect(await screen.findByText('Informe sua senha atual.')).toBeInTheDocument();
  expect(screen.getByText('Informe sua nova senha.')).toBeInTheDocument();
  expect(screen.getByText('Confirme sua nova senha.')).toBeInTheDocument();
  expect(put).not.toHaveBeenCalled();
});

it('exige 8 caracteres e a mesma senha na confirmação', async () => {
  const put = mockApi('PUT');
  const form = renderSettings();

  await userEvent.type(form.current, 'senha-antiga');
  await userEvent.type(form.password, '1234567');
  await userEvent.type(form.confirmation, '7654321');
  await form.save();

  expect(await screen.findByText('Use pelo menos 8 caracteres.')).toBeInTheDocument();
  expect(screen.getByText('As senhas informadas não coincidem.')).toBeInTheDocument();
  expect(put).not.toHaveBeenCalled();
});

it('troca a senha, limpa os campos e avisa', async () => {
  const put = mockApi('PUT', reply(204));
  const form = renderSettings();

  await userEvent.type(form.current, 'senha-antiga');
  await userEvent.type(form.password, 'senha-nova-1');
  await userEvent.type(form.confirmation, 'senha-nova-1');
  await form.save();

  expect(await screen.findByText('Senha alterada com sucesso.')).toBeInTheDocument();
  expect(put).toHaveBeenCalledWith('/me/password', { body: { current_password: 'senha-antiga', password: 'senha-nova-1', password_confirmation: 'senha-nova-1' } });
  expect(screen.getByLabelText(/^Senha atual/)).toHaveValue('');
  expect(screen.getByLabelText(/^Nova senha/)).toHaveValue('');
});

it('senha atual errada: mostra no campo e o limpa', async () => {
  mockApi('PUT', reply(422, { message: 'x', errors: { current_password: ['A senha atual informada está incorreta.'] } }));
  const form = renderSettings();

  await userEvent.type(form.current, 'errada-123');
  await userEvent.type(form.password, 'senha-nova-1');
  await userEvent.type(form.confirmation, 'senha-nova-1');
  await form.save();

  expect(await screen.findByText('A senha atual informada está incorreta.')).toBeInTheDocument();
  expect(screen.getByLabelText(/^Senha atual/)).toHaveValue('');
});

it('falha inesperada: avisa sem perder o que foi digitado', async () => {
  mockApi('PUT', reply(500, {}));
  const form = renderSettings();

  await userEvent.type(form.current, 'senha-antiga');
  await userEvent.type(form.password, 'senha-nova-1');
  await userEvent.type(form.confirmation, 'senha-nova-1');
  await form.save();

  expect(await screen.findByText('Não foi possível alterar sua senha agora. Tente novamente.')).toBeInTheDocument();
  expect(form.password).toHaveValue('senha-nova-1');
});

it('ao sair com alterações não salvas, pergunta antes e respeita a escolha', async () => {
  const form = renderSettings();
  await userEvent.type(form.current, 'senha-antiga');

  await act(() => form.router.navigate('/home'));
  expect(await screen.findByText('As alterações feitas ainda não foram salvas.')).toBeInTheDocument();
  await userEvent.click(screen.getByRole('button', { name: 'Continuar editando' }));
  expect(form.router.state.location.pathname).toBe('/configuracoes');
  expect(form.current).toHaveValue('senha-antiga');

  await act(() => form.router.navigate('/home'));
  await userEvent.click(await screen.findByRole('button', { name: 'Descartar alterações' }));
  expect(await screen.findByText('Página inicial')).toBeInTheDocument();
});
