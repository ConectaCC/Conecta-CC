import { act, waitFor } from '@testing-library/react';
import { expect, it } from 'vitest';
import { mockApi, reply } from '@/test/api';
import { renderHookWithClient } from '@/test/render';
import { useStudies } from './useStudies';

const PROFILE = { disciplines: [{ id: 1 }] };
const DISCIPLINES = [{ id: 1 }, { id: 2 }];

it('tentar de novo busca só o que falhou', async () => {
  const get = mockApi('GET');
  get.mockImplementation(((path: string) =>
    Promise.resolve(path === '/profile' ? reply(500, {}) : reply(200, DISCIPLINES))) as never);
  const { result } = renderHookWithClient(() => useStudies());
  await waitFor(() => expect(result.current.failed).toBe(true));
  get.mockClear();

  act(() => result.current.retry());

  expect(get.mock.calls.map((args: unknown[]) => args[0])).toEqual(['/profile']);
});

it('tentar de novo busca as disciplinas quando só elas falharam', async () => {
  const get = mockApi('GET');
  get.mockImplementation(((path: string) =>
    Promise.resolve(path === '/profile' ? reply(200, PROFILE) : reply(500, {}))) as never);
  const { result } = renderHookWithClient(() => useStudies());
  await waitFor(() => expect(result.current.failed).toBe(true));
  get.mockClear();

  act(() => result.current.retry());

  expect(get.mock.calls.map((args: unknown[]) => args[0])).toEqual(['/disciplines']);
});

it('marca as disciplinas da pessoa e fica pronto quando as duas respostas chegam', async () => {
  const get = mockApi('GET');
  get.mockImplementation(((path: string) => Promise.resolve(reply(200, path === '/profile' ? PROFILE : DISCIPLINES))) as never);
  const { result } = renderHookWithClient(() => useStudies());

  await waitFor(() => expect(result.current.ready).toBe(true));
  expect([...result.current.mineIds]).toEqual([1]);
  expect(result.current.failed).toBe(false);
});
