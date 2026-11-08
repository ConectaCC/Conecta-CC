import { screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { Route, Routes, useNavigationType } from 'react-router';
import { expect, it } from 'vitest';
import { failNetwork, mockApi, reply } from '@/test/api';
import { renderWithProviders } from '@/test/render';
import { LoginPage } from './LoginPage';

function Home() {
  // REPLACE: voltar no navegador não leva de novo ao Login
  return <p>Página inicial ({useNavigationType()})</p>;
}

function renderLogin() {
  renderWithProviders(
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route path="/home" element={<Home />} />
    </Routes>,
    { route: '/login' },
  );
  return {
    email: screen.getByLabelText('E-mail institucional'),
    password: screen.getByLabelText('Senha'),
    submit: () => userEvent.click(screen.getByRole('button', { name: 'Entrar' })),
  };
}

it('valida os campos antes de chamar a API', async () => {
  const post = mockApi('POST');
  const form = renderLogin();

  await form.submit();

  expect(await screen.findByText('Informe um endereço de e-mail válido.')).toBeInTheDocument();
  expect(screen.getByText('Informe sua senha.')).toBeInTheDocument();
  expect(post).not.toHaveBeenCalled();
});

it('recusa e-mail sem domínio', async () => {
  const post = mockApi('POST');
  const form = renderLogin();

  await userEvent.type(form.email, 'aluno@ifsul');
  await userEvent.type(form.password, 'segredo123');
  await form.submit();

  expect(await screen.findByText('Informe um endereço de e-mail válido.')).toBeInTheDocument();
  expect(post).not.toHaveBeenCalled();
});

it('entra e vai para a página inicial', async () => {
  const post = mockApi('POST', reply(200, { id: 1, name: 'Ana', email: 'a@academico.ifsul.edu.br', display_name: 'Ana', initials: 'A', role: 'ALUNO', profile_complete: false }));
  const form = renderLogin();

  await userEvent.type(form.email, 'aluno@academico.ifsul.edu.br');
  await userEvent.type(form.password, 'segredo123');
  await form.submit();

  expect(await screen.findByText('Página inicial (REPLACE)')).toBeInTheDocument();
  expect(post).toHaveBeenCalledWith('/login', { body: { email: 'aluno@academico.ifsul.edu.br', password: 'segredo123' } });
});

it('credenciais erradas: mostra a mensagem única e limpa a senha', async () => {
  mockApi('POST', reply(422, { message: 'E-mail ou senha incorretos.', errors: { credentials: ['E-mail ou senha incorretos.'] } }));
  const form = renderLogin();

  await userEvent.type(form.email, 'aluno@academico.ifsul.edu.br');
  await userEvent.type(form.password, 'errada123');
  await form.submit();

  expect(await screen.findByText('E-mail ou senha incorretos.')).toBeInTheDocument();
  expect(form.password).toHaveValue('');
  expect(form.password).toHaveFocus();
});

it('erro de campo vindo da API vai para o campo', async () => {
  mockApi('POST', reply(422, { message: 'x', errors: { email: ['Informe um endereço de e-mail válido.'] } }));
  const form = renderLogin();

  await userEvent.type(form.email, 'aluno@academico.ifsul.edu.br');
  await userEvent.type(form.password, 'segredo123');
  await form.submit();

  expect(await screen.findByText('Informe um endereço de e-mail válido.')).toBeInTheDocument();
});

it('muitas tentativas: mostra a mensagem da API', async () => {
  mockApi('POST', reply(429, { message: 'Muitas tentativas. Tente novamente em 42 segundos.' }));
  const form = renderLogin();

  await userEvent.type(form.email, 'aluno@academico.ifsul.edu.br');
  await userEvent.type(form.password, 'segredo123');
  await form.submit();

  expect(await screen.findByText('Muitas tentativas. Tente novamente em 42 segundos.')).toBeInTheDocument();
});

it('sem conexão: avisa e oferece tentar de novo', async () => {
  const post = failNetwork('POST');
  const form = renderLogin();

  await userEvent.type(form.email, 'aluno@academico.ifsul.edu.br');
  await userEvent.type(form.password, 'segredo123');
  await form.submit();

  expect(await screen.findByText('Não foi possível conectar ao ConectaCC. Verifique sua conexão e tente novamente.')).toBeInTheDocument();
  await userEvent.click(screen.getByRole('button', { name: 'Tentar novamente' }));
  expect(post).toHaveBeenCalledTimes(2);
});

it('depois do cadastro, traz o e-mail preenchido, avisa e foca a senha', async () => {
  renderWithProviders(
    <Routes>
      <Route path="/login" element={<LoginPage />} />
    </Routes>,
    { route: { pathname: '/login', state: { registeredEmail: 'ana@academico.ifsul.edu.br' } } },
  );

  expect(screen.getByLabelText('E-mail institucional')).toHaveValue('ana@academico.ifsul.edu.br');
  expect(screen.getByText('Conta criada. Entre com seu e-mail e senha.')).toBeInTheDocument();
  await waitFor(() => expect(screen.getByLabelText('Senha')).toHaveFocus());
});
