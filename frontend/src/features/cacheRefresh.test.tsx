// depois de cada ação, as telas que mostram o item mudado buscam de novo os dados
import { act } from '@testing-library/react';
import { expect, it } from 'vitest';
import { useReviseMaterial } from '@/features/materials/hooks';
import { useApprove, useReject, type QueueItem } from '@/features/moderation/hooks';
import { useCancelMaterial, useCancelOpportunity, useDeleteMaterial, useDeleteOpportunity } from '@/features/my-contents/hooks';
import { useAddOpportunity } from '@/features/opportunities/hooks';
import { useUpdateProfile } from '@/features/profile/hooks';
import { mockApi, reply } from '@/test/api';
import { renderHookWithClient } from '@/test/render';

const material = { kind: 'material', item: { id: 5, discipline: { id: 4 } } } as unknown as QueueItem;
const opportunity = { kind: 'opportunity', item: { id: 6 } } as unknown as QueueItem;
const MATERIAL_DECIDED = [['moderation', 'materials'], ['materials', 4], ['community']];
const AUTHOR_MATERIAL = [['my-materials'], ['materials', 4], ['moderation', 'materials']];
const AUTHOR_OPPORTUNITY = [['my-opportunities'], ['opportunities'], ['moderation', 'opportunities'], ['community']];

async function refreshedKeys<V>(useHook: () => { mutateAsync: (variables: V) => Promise<unknown> }, method: 'POST' | 'PUT' | 'DELETE', variables: V) {
  mockApi(method, reply(200, {}));
  const { result, invalidatedKeys } = renderHookWithClient(useHook);
  await act(() => result.current.mutateAsync(variables));
  return invalidatedKeys();
}

it('a alteração de material atualiza Meus conteúdos e a fila de Moderação', async () => {
  const body = { title: 'T', description: null, url: 'https://a.com', anonymous: false };
  expect(await refreshedKeys(useReviseMaterial, 'PUT', { id: 9, body })).toEqual([['my-materials'], ['moderation', 'materials']]);
});

it('aprovar ou rejeitar material atualiza a fila, a disciplina e o mural', async () => {
  expect(await refreshedKeys(useApprove, 'POST', material)).toEqual(MATERIAL_DECIDED);
  expect(await refreshedKeys(useReject, 'POST', { queued: material, reason: 'x' })).toEqual(MATERIAL_DECIDED);
});

it('decidir uma oportunidade atualiza a fila, a página de Oportunidades e o mural', async () => {
  expect(await refreshedKeys(useApprove, 'POST', opportunity)).toEqual([['moderation', 'opportunities'], ['opportunities'], ['community']]);
});

it('cancelar ou excluir material atualiza Meus conteúdos, a disciplina e a fila', async () => {
  expect(await refreshedKeys(useCancelMaterial, 'POST', { id: 9, discipline: { id: 4 } })).toEqual(AUTHOR_MATERIAL);
  expect(await refreshedKeys(useDeleteMaterial, 'DELETE', { id: 9, discipline: { id: 4 } })).toEqual(AUTHOR_MATERIAL);
});

it('cancelar ou excluir oportunidade atualiza Minhas oportunidades, a página, a fila e o mural', async () => {
  expect(await refreshedKeys(useCancelOpportunity, 'POST', { id: 6 })).toEqual(AUTHOR_OPPORTUNITY);
  expect(await refreshedKeys(useDeleteOpportunity, 'DELETE', { id: 6 })).toEqual(AUTHOR_OPPORTUNITY);
});

it('indicar uma oportunidade atualiza Minhas oportunidades e a Moderação', async () => {
  const body = { type: 'ESTAGIO', title: 'T', description: 'D', url: 'https://a.com', modality: 'REMOTO', deadline: null } as const;
  expect(await refreshedKeys(useAddOpportunity, 'POST', body)).toEqual([['my-opportunities'], ['moderation']]);
});

it('salvar o Perfil guarda a resposta e atualiza o usuário do cabeçalho', async () => {
  const saved = { display_name: 'Ana' };
  mockApi('PUT', reply(200, saved));
  const { result, client, invalidatedKeys } = renderHookWithClient(() => useUpdateProfile());

  await act(() => result.current.mutateAsync({ display_name: 'Ana', discipline_ids: [1], about: null, interests: [], can_help_with: [], needs_help_with: [], github_url: null, linkedin_url: null }));

  expect(client.getQueryData(['profile'])).toEqual(saved);
  expect(invalidatedKeys()).toEqual([['me']]);
});
