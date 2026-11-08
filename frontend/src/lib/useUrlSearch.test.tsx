import { act, renderHook } from '@testing-library/react';
import { BrowserRouter } from 'react-router';
import { beforeEach, expect, it, vi } from 'vitest';
import { useUrlParams, useUrlSearch } from './useUrlSearch';

function setup(url: string) {
  window.history.replaceState(null, '', url);
  return renderHook(() => ({ search: useUrlSearch('busca'), url: useUrlParams() }), { wrapper: BrowserRouter });
}

beforeEach(() => {
  vi.useFakeTimers();
});

it('começa com o texto que está no endereço', () => {
  const { result } = setup('/estudos?busca=calculo');

  expect(result.current.search.draft).toBe('calculo');
  expect(result.current.search.term).toBe('calculo');
});

it('só leva o texto ao endereço 300 ms depois da última tecla, sem os espaços das pontas', () => {
  const { result } = setup('/estudos');

  act(() => result.current.search.setDraft(' grafos '));
  act(() => {
    vi.advanceTimersByTime(299);
  });
  expect(window.location.search).toBe('');

  act(() => {
    vi.advanceTimersByTime(1);
  });
  expect(window.location.search).toBe('?busca=grafos');
  expect(result.current.search.term).toBe('grafos');
});

it('tira a busca do endereço quando o texto fica vazio', () => {
  const { result } = setup('/estudos?busca=grafos');

  act(() => result.current.search.setDraft('   '));
  act(() => {
    vi.advanceTimersByTime(300);
  });

  expect(window.location.search).toBe('');
});

it('não desfaz um filtro escolhido durante o atraso da busca', () => {
  const { result } = setup('/oportunidade');

  act(() => result.current.search.setDraft('estagio'));
  act(() => result.current.url.setParam('tipo', 'EVENTO'));
  act(() => {
    vi.advanceTimersByTime(300);
  });

  expect(new URLSearchParams(window.location.search).get('tipo')).toBe('EVENTO');
  expect(new URLSearchParams(window.location.search).get('busca')).toBe('estagio');
});

it('limpa o texto e o endereço juntos', () => {
  const { result } = setup('/estudos?busca=grafos&tipo=EVENTO');

  act(() => result.current.search.clear());

  expect(result.current.search.draft).toBe('');
  expect(window.location.search).toBe('?tipo=EVENTO');
});

it('troca o endereço sem criar entrada no histórico', () => {
  const { result } = setup('/estudos');
  const before = window.history.length;

  act(() => result.current.url.setParam('tipo', 'EVENTO'));

  expect(window.history.length).toBe(before);
});
