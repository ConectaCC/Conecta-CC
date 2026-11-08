import { screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { Route, Routes, useLocation } from 'react-router';
import { expect, it } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderWithProviders } from '@/test/render';
import { RegisterPage } from './RegisterPage';

const TERMS = { version: '2026-11', title: 'Termos de participação', sections: [{ title: 'Uso', body: 'Respeite.' }], acceptance_label: 'Li e aceito os Termos.' };

function LoginProbe() {
  const location = useLocation();
  return <p>Login com {(location.state as { registeredEmail: string }).registeredEmail}</p>;
}

function renderRegister() {
  renderWithProviders(
    <Routes>
      <Route path="/cadastro" element={<RegisterPage />} />
      <Route path="/login" element={<LoginProbe />} />
    </Routes>,
    { route: '/cadastro' },
  );
}

async function fill({ name = 'Ana Silva', email = 'Ana@academico.ifsul.edu.br', password = 'segredo123', confirmation = 'segredo123' } = {}) {
  if (name) await userEvent.type(screen.getByLabelText('Nome completo'), name);
  if (email) await userEvent.type(screen.getByLabelText('E-mail institucional'), email);
  if (password) await userEvent.type(screen.getByLabelText('Senha'), password);
  if (confirmation) await userEvent.type(screen.getByLabelText('Confirmar senha'), confirmation);
  await userEvent.click(screen.getByRole('button', { name: 'Criar minha conta' }));
}

it('valida todos os campos com as mensagens do protótipo, sem abrir os Termos', async () => {
  mockApi('GET');
  renderRegister();

  await fill({ name: '   ', email: 'ana@', password: '1234567', confirmation: '7654321' });

  expect(await screen.findByText('Informe seu nome completo.')).toBeInTheDocument();
  expect(screen.getByText('Informe um endereço de e-mail válido.')).toBeInTheDocument();
  expect(screen.getByText('Use pelo menos 8 caracteres.')).toBeInTheDocument();
  expect(screen.getByText('As senhas informadas não coincidem.')).toBeInTheDocument();
  expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
});

it('exige a senha e a confirmação mesmo vazias', async () => {
  mockApi('GET');
  renderRegister();

  await fill({ password: '', confirmation: '' });

  expect(await screen.findByText('Use pelo menos 8 caracteres.')).toBeInTheDocument();
  expect(screen.getByText('As senhas informadas não coincidem.')).toBeInTheDocument();
});

it('cria a conta depois do aceite e leva ao Login com o e-mail em minúsculas', async () => {
  mockApi('GET', reply(200, TERMS));
  const post = mockApi('POST', reply(201, { message: 'Conta criada.' }));
  renderRegister();

  await fill();
  const dialog = await screen.findByRole('dialog');
  const create = within(dialog).getByRole('button', { name: 'Concordar e criar conta' });
  expect(create).toBeDisabled();
  await userEvent.click(await within(dialog).findByLabelText('Li e aceito os Termos.'));
  await userEvent.click(create);

  expect(await screen.findByText('Login com ana@academico.ifsul.edu.br')).toBeInTheDocument();
  expect(post).toHaveBeenCalledWith('/register', {
    body: { name: 'Ana Silva', email: 'Ana@academico.ifsul.edu.br', password: 'segredo123', password_confirmation: 'segredo123', terms_version: '2026-11' },
  });
});

it('e-mail já cadastrado: fecha os Termos e oferece entrar', async () => {
  mockApi('GET', reply(200, TERMS));
  mockApi('POST', reply(422, { message: 'x', errors: { email: ['Não foi possível criar uma conta com este e-mail. Se você já possui cadastro, entre.'] } }));
  renderRegister();

  await fill();
  const dialog = await screen.findByRole('dialog');
  await userEvent.click(await within(dialog).findByLabelText('Li e aceito os Termos.'));
  await userEvent.click(within(dialog).getByRole('button', { name: 'Concordar e criar conta' }));

  expect(await screen.findByText(/Não foi possível criar uma conta com este e-mail/)).toBeInTheDocument();
  expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
  expect(screen.getByRole('link', { name: 'entre' })).toHaveAttribute('href', '/login');
});
