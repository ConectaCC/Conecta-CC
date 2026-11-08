import { act, renderHook } from '@testing-library/react';
import { BrowserRouter } from 'react-router';
import { afterEach, beforeEach, expect, it, vi } from 'vitest';
import { useScrollToHashItem } from './useScrollToHashItem';

type Query = Parameters<typeof useScrollToHashItem>[2];

const idle = (overrides: Partial<Query> = {}): Query => ({
  hasNextPage: true,
  isFetchingNextPage: false,
  isFetchNextPageError: false,
  fetchNextPage: vi.fn(),
  ...overrides,
});

function setup(hash: string, ids: number[], query: Query) {
  window.history.replaceState(null, '', `/estudos/algoritmos-i${hash}`);
  return renderHook(({ ids, query }) => useScrollToHashItem('material', ids, query), {
    wrapper: BrowserRouter,
    initialProps: { ids, query },
  });
}

let item: HTMLElement;
const scrollIntoView = vi.fn();

beforeEach(() => {
  item = document.createElement('article');
  item.id = 'material-5';
  document.body.append(item);
  Element.prototype.scrollIntoView = scrollIntoView;
});

afterEach(() => {
  item.remove();
  scrollIntoView.mockClear();
});

it('carrega a próxima página enquanto o item do endereço não aparece', () => {
  const query = idle();
  setup('#material-5', [1, 2], query);

  expect(query.fetchNextPage).toHaveBeenCalledOnce();
});

it('não carrega mais páginas quando não há próxima, quando já está carregando ou quando a última falhou', () => {
  for (const state of [{ hasNextPage: false }, { isFetchingNextPage: true }, { isFetchNextPageError: true }]) {
    const query = idle(state);
    setup('#material-5', [1, 2], query).unmount();
    expect(query.fetchNextPage).not.toHaveBeenCalled();
  }
});

it('ignora âncora de outro tipo de item ou sem número', () => {
  for (const hash of ['#oportunidade-5', '#material-', '#material-5a', '']) {
    const query = idle();
    setup(hash, [1, 2], query).unmount();
    expect(query.fetchNextPage).not.toHaveBeenCalled();
  }
});

it('rola até o item uma vez e o destaca por 2 segundos', () => {
  vi.useFakeTimers();
  const query = idle();
  const { rerender } = setup('#material-5', [1, 2], query);

  rerender({ ids: [1, 2, 5], query });
  rerender({ ids: [1, 2, 5, 6], query });

  expect(query.fetchNextPage).toHaveBeenCalledOnce();
  expect(scrollIntoView).toHaveBeenCalledOnce();
  expect(scrollIntoView).toHaveBeenCalledWith({ block: 'center' });
  expect(item).toHaveClass('ring-4', 'ring-glow');

  act(() => {
    vi.advanceTimersByTime(1999);
  });
  expect(item).toHaveClass('ring-4');
  act(() => {
    vi.advanceTimersByTime(1);
  });
  expect(item).not.toHaveClass('ring-4');
  expect(item).not.toHaveClass('ring-glow');
});
