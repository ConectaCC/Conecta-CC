import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router';

const SEARCH_DELAY_MS = 300;

// muda um parâmetro partindo do endereço atual, e não da última renderização:
// a busca com atraso não desfaz um filtro escolhido nesse meio-tempo
export function useUrlParams() {
  const [params, setParams] = useSearchParams();

  function setParam(key: string, value: string | null) {
    const next = new URLSearchParams(window.location.search);
    if (value) next.set(key, value);
    else next.delete(key);
    setParams(next, { replace: true });
  }

  return { params, setParam };
}

// texto da busca: vai para o endereço (e para a API) só quando a pessoa para de digitar
export function useUrlSearch(key: string) {
  const { params, setParam } = useUrlParams();
  const [draft, setDraft] = useState(params.get(key) ?? '');
  const term = (params.get(key) ?? '').trim();

  useEffect(() => {
    const value = draft.trim();
    if (value === (new URLSearchParams(window.location.search).get(key) ?? '')) return;
    const timer = setTimeout(() => setParam(key, value || null), SEARCH_DELAY_MS);
    return () => clearTimeout(timer);
    // eslint-disable-next-line react-hooks/exhaustive-deps -- setParam muda a cada render; o gatilho é só o texto
  }, [draft, key]);

  function clear() {
    setDraft('');
    setParam(key, null);
  }

  return { draft, setDraft, term, clear };
}
