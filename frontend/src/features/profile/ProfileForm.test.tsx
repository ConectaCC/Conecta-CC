import { screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { expect, it, vi } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderWithDataRouter } from '@/test/render';
import type { Discipline, Profile } from './hooks';
import { ProfileForm } from './ProfileForm';

const ALGORITMOS: Discipline = { id: 1, name: 'Algoritmos I', slug: 'algoritmos-i', semester: 1, workload_hours: 75, syllabus_url: null };

const profile = (overrides: Partial<Profile> = {}): Profile => ({
  display_name: 'Ana Silva',
  initials: 'AS',
  about: null,
  interests: [],
  can_help_with: [],
  needs_help_with: [],
  github_url: null,
  linkedin_url: null,
  disciplines: [ALGORITMOS],
  complete: true,
  ...overrides,
});

function renderForm(current: Profile, onSaved = vi.fn()) {
  renderWithDataRouter(<ProfileForm profile={current} disciplines={[ALGORITMOS]} focusDisciplines={false} onSaved={onSaved} onCancel={vi.fn()} />, { path: '/perfil' });
  return { save: () => userEvent.click(screen.getByRole('button', { name: 'Salvar alterações' })) };
}

it('exige o nome de exibição e uma disciplina, sem chamar a API', async () => {
  const put = mockApi('PUT');
  const form = renderForm(profile({ display_name: '', disciplines: [] }));

  await userEvent.type(screen.getByLabelText(/^Nome de exibição/), '   ');
  await form.save();

  expect(await screen.findByText('Informe seu nome de exibição.')).toBeInTheDocument();
  expect(screen.getByText('Selecione pelo menos uma disciplina atual para completar seu perfil.')).toBeInTheDocument();
  expect(put).not.toHaveBeenCalled();
});

it('salva com textos opcionais vazios como null', async () => {
  const put = mockApi('PUT', reply(200, profile()));
  const onSaved = vi.fn();
  const form = renderForm(profile(), onSaved);

  await userEvent.type(screen.getByLabelText('GitHub'), '   ');
  await form.save();

  await vi.waitFor(() => expect(onSaved).toHaveBeenCalledOnce());
  expect(put).toHaveBeenCalledWith('/profile', {
    body: { display_name: 'Ana Silva', discipline_ids: [1], about: null, interests: [], can_help_with: [], needs_help_with: [], github_url: null, linkedin_url: null },
  });
});

it('mostra no campo o erro de link vindo da API', async () => {
  mockApi('PUT', reply(422, { message: 'x', errors: { github_url: ['Informe o endereço do seu perfil no GitHub, como https://github.com/usuario.'] } }));
  const form = renderForm(profile());

  await userEvent.type(screen.getByLabelText('GitHub'), 'github.com/ana/repo');
  await form.save();

  expect(await screen.findByText('Informe o endereço do seu perfil no GitHub, como https://github.com/usuario.')).toBeInTheDocument();
});

it('atualiza a prévia das iniciais enquanto digita o nome', async () => {
  renderForm(profile());

  const name = screen.getByLabelText(/^Nome de exibição/);
  await userEvent.clear(name);
  await userEvent.type(name, 'Bruno Costa');

  expect(screen.getByText('BC')).toBeInTheDocument();
});
