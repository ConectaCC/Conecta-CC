import { screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useNavigationType } from 'react-router';
import { expect, it } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderWithDataRouter } from '@/test/render';
import { Sidebar } from './Sidebar';

function Login() {
  return <p>Login ({useNavigationType()})</p>;
}

it('sair encerra a sessão e troca a página, sem deixar voltar para a área logada', async () => {
  const post = mockApi('POST', reply(204));
  renderWithDataRouter(<Sidebar />, { path: '/home', routes: [{ path: '/login', element: <Login /> }] });

  await userEvent.click(screen.getByRole('button', { name: 'Sair' }));

  expect(await screen.findByText('Login (REPLACE)')).toBeInTheDocument();
  expect(post).toHaveBeenCalledWith('/logout');
});

it('sai mesmo sem resposta da API', async () => {
  mockApi('POST', reply(500, {}));
  renderWithDataRouter(<Sidebar />, { path: '/home', routes: [{ path: '/login', element: <Login /> }] });

  await userEvent.click(screen.getByRole('button', { name: 'Sair' }));

  expect(await screen.findByText('Login (REPLACE)')).toBeInTheDocument();
});
