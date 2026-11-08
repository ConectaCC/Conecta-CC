import { Briefcase, ExternalLink, GraduationCap, Loader2, Plus, Search } from 'lucide-react';
import { useState } from 'react';
import { FormAlert } from '@/components/FormAlert';
import { Skeleton } from '@/components/Skeleton';
import { Button } from '@/components/ui/button';
import { useScrollToHashItem } from '@/lib/useScrollToHashItem';
import { useUrlParams, useUrlSearch } from '@/lib/useUrlSearch';
import { AddOpportunityDialog } from './AddOpportunityDialog';
import { useOpportunities } from './hooks';
import { TYPE_LABELS } from './labels';
import { OpportunityCard } from './OpportunityCard';

// link confirmado pelo time (decisoes.md §70); o card só redireciona, nada é copiado
const IFSUL_SCHOLARSHIPS_URL = 'https://www.ifsul.edu.br/estudante-pronatec';

export function OpportunitiesPage() {
  const { params, setParam } = useUrlParams();
  const { draft, setDraft, term, clear } = useUrlSearch('busca');
  const type = params.get('tipo') && TYPE_LABELS[params.get('tipo') as string] ? params.get('tipo') : null;
  const list = useOpportunities(term, type);
  const items = list.data?.pages.flatMap((page) => page.data) ?? [];
  const [adding, setAdding] = useState(false);
  const filtered = term !== '' || type !== null;

  useScrollToHashItem('oportunidade', items.map((item) => item.id), list);

  function clearAll() {
    clear();
    setParam('tipo', null);
  }

  const addButton = (
    <Button type="button" onClick={() => setAdding(true)} className="h-11 gap-2 px-5 text-[15px] font-semibold">
      <Plus className="size-5" aria-hidden />
      Adicionar oportunidade
    </Button>
  );

  return (
    <div>
      <header className="flex items-center gap-5 rounded-2xl border border-blue-200/70 bg-gradient-to-r from-[#dfeafb] via-[#eaf2fc] to-white px-6 py-7 sm:px-8">
        <span aria-hidden className="hidden size-16 shrink-0 place-items-center rounded-2xl bg-white/70 text-action sm:grid">
          <Briefcase className="size-8" />
        </span>
        <div className="min-w-0">
          <h1 className="font-display text-4xl font-bold text-foreground">Oportunidade</h1>
          <p className="mt-2 text-lg text-muted-foreground">
            Encontre oportunidades para complementar sua formação acadêmica e profissional. Aqui você encontra recursos do IFSul e indicações de
            estudantes aprovadas pela moderação.
          </p>
        </div>
      </header>

      <section aria-labelledby="ifsul" className="mt-8">
        <h2 id="ifsul" className="font-display text-xl font-bold text-foreground">
          Oportunidades do IFSul
        </h2>
        <p className="mt-1 text-[15px] text-muted-foreground">Acessos a páginas e recursos institucionais já existentes.</p>
        <article className="mt-4 flex flex-col gap-3 rounded-2xl border border-blue-200/70 bg-white p-5 sm:max-w-xl">
          <div className="flex items-center gap-3">
            <span aria-hidden className="grid size-10 shrink-0 place-items-center rounded-xl bg-navy-900 text-white">
              <GraduationCap className="size-5" />
            </span>
            <div className="min-w-0">
              <h3 className="font-display text-lg leading-snug font-bold text-foreground">Conheça as oportunidades de bolsas do IFSul</h3>
              <p className="text-xs font-semibold tracking-wide text-navy-900">Fonte oficial · IFSul</p>
            </div>
          </div>
          <p className="text-[15px] text-muted-foreground">Consulte bolsas, programas e oportunidades disponibilizadas oficialmente pelo IFSul.</p>
          <a
            href={IFSUL_SCHOLARSHIPS_URL}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-flex items-center gap-1.5 self-start text-[15px] font-semibold text-action underline-offset-2 hover:underline"
          >
            Ver oportunidades
            <ExternalLink className="size-4" aria-hidden />
            <span className="sr-only">(abre em nova aba, ifsul.edu.br)</span>
          </a>
        </article>
      </section>

      <section aria-labelledby="indicadas" className="mt-10">
        <div className="flex flex-wrap items-end justify-between gap-4">
          <div className="max-w-2xl">
            <h2 id="indicadas" className="font-display text-xl font-bold text-foreground">
              Oportunidades indicadas pela comunidade
            </h2>
            <p className="mt-1 text-[15px] text-muted-foreground">Veja oportunidades compartilhadas por estudantes e aprovadas pela moderação do ConectaCC.</p>
          </div>
          {addButton}
        </div>

        <div className="mt-5 flex flex-wrap items-center gap-3">
          <div className="relative min-w-0 flex-[1_1_16rem]">
            <label htmlFor="busca-oportunidades" className="sr-only">
              Buscar oportunidades
            </label>
            <Search className="pointer-events-none absolute top-1/2 left-3.5 size-5 -translate-y-1/2 text-muted-foreground" aria-hidden />
            <input
              id="busca-oportunidades"
              type="search"
              value={draft}
              maxLength={100}
              onChange={(event) => setDraft(event.target.value)}
              placeholder="Buscar oportunidades…"
              className="h-11 w-full rounded-lg border border-input bg-white pr-3 pl-11 text-base text-foreground placeholder:text-muted-foreground"
            />
          </div>
          <label htmlFor="tipo-oportunidades" className="sr-only">
            Tipo
          </label>
          <select
            id="tipo-oportunidades"
            value={type ?? ''}
            onChange={(event) => setParam('tipo', event.target.value || null)}
            className="h-11 rounded-lg border border-input bg-white px-3 text-[15px] text-foreground"
          >
            <option value="">Todos os tipos</option>
            {Object.entries(TYPE_LABELS).map(([value, label]) => (
              <option key={value} value={value}>
                {label}
              </option>
            ))}
          </select>
        </div>

        <div className="mt-5 space-y-4">
          {list.isPending && (
            <>
              <span role="status" className="sr-only">
                Carregando oportunidades…
              </span>
              <div className="grid gap-4 md:grid-cols-2">
                <Skeleton className="h-48 rounded-2xl" />
                <Skeleton className="h-48 rounded-2xl" />
              </div>
            </>
          )}

          {/* também com itens na tela ("Mostrar mais" que falhou): o aviso aparece e os itens ficam */}
          {list.isError && (
            <FormAlert
              variant="warning"
              action={
                <button
                  type="button"
                  onClick={() => (list.isFetchNextPageError ? list.fetchNextPage() : list.refetch())}
                  className="font-semibold underline underline-offset-2"
                >
                  Tentar novamente
                </button>
              }
            >
              Não foi possível carregar as oportunidades agora.
            </FormAlert>
          )}

          {list.data && items.length === 0 && filtered && (
            <div role="status" className="rounded-2xl border border-dashed border-glow bg-white p-7">
              <h3 className="font-display text-lg font-bold text-foreground">Nenhuma oportunidade encontrada</h3>
              <p className="mt-2 text-base text-muted-foreground">Tente buscar por outro termo.</p>
              <button type="button" onClick={clearAll} className="mt-4 font-semibold text-action underline-offset-2 hover:underline">
                Limpar busca e filtro
              </button>
            </div>
          )}

          {list.data && items.length === 0 && !filtered && (
            <div className="rounded-2xl border border-dashed border-glow bg-white p-7">
              <h3 className="font-display text-lg font-bold text-foreground">Nenhuma oportunidade compartilhada por enquanto</h3>
              <p className="mt-2 text-base text-muted-foreground">Conhece uma oportunidade que pode ajudar outros estudantes? Compartilhe com a comunidade.</p>
              <div className="mt-5">{addButton}</div>
            </div>
          )}

          {items.length > 0 && (
            <ul className="grid gap-4 md:grid-cols-2">
              {items.map((item) => (
                <li key={item.id}>
                  <OpportunityCard item={item} />
                </li>
              ))}
            </ul>
          )}

          {list.hasNextPage && !list.isFetchNextPageError && (
            <div className="flex justify-center">
              <Button
                type="button"
                variant="outline"
                onClick={() => list.fetchNextPage()}
                disabled={list.isFetchingNextPage}
                className="h-11 border-action px-5 text-[15px] font-semibold text-action"
              >
                {list.isFetchingNextPage && <Loader2 className="size-5 animate-spin" aria-hidden />}
                Mostrar mais
              </Button>
            </div>
          )}
        </div>
      </section>

      <AddOpportunityDialog open={adding} onOpenChange={setAdding} backLabel="Voltar para Oportunidade" />
    </div>
  );
}
