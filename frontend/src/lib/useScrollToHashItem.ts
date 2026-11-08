import { useEffect, useRef } from 'react';
import { useLocation } from 'react-router';

type PagedQuery = {
  hasNextPage: boolean;
  isFetchingNextPage: boolean;
  isFetchNextPageError: boolean;
  fetchNextPage: () => unknown;
};

// link com #<prefixo>-<id> (vindo do mural ou de uma notificação): carrega as próximas páginas
// até achar o item, rola até ele e o destaca por 2 segundos
export function useScrollToHashItem(prefix: string, ids: number[], query: PagedQuery) {
  const { hash } = useLocation();
  const match = new RegExp(`^#${prefix}-(\\d+)$`).exec(hash);
  const target = match ? Number(match[1]) : null;
  const found = target !== null && ids.includes(target);
  const scrolled = useRef<number | null>(null);

  useEffect(() => {
    if (target === null || found || !query.hasNextPage || query.isFetchingNextPage || query.isFetchNextPageError) return;
    query.fetchNextPage();
  }, [target, found, query]);

  useEffect(() => {
    if (!found || scrolled.current === target) return;
    scrolled.current = target;
    const element = document.getElementById(`${prefix}-${target}`);
    element?.scrollIntoView({ block: 'center' });
    // destaque passageiro: só visual, por isso direto no elemento e não em estado
    element?.classList.add('ring-4', 'ring-glow');
    const timer = setTimeout(() => element?.classList.remove('ring-4', 'ring-glow'), 2000);
    return () => clearTimeout(timer);
  }, [found, target, prefix]);
}
