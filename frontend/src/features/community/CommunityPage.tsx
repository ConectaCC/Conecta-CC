import { LayoutGrid, List, Loader2, Search, Users } from 'lucide-react';
import { Link } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { Skeleton } from '@/components/Skeleton';
import { Button } from '@/components/ui/button';
import { useUrlParams, useUrlSearch } from '@/lib/useUrlSearch';
import { cn } from '@/lib/utils';
import { CommunityAside } from './CommunityAside';
import { FeedEntry } from './FeedEntry';
import { useCommunityFeed, type FeedOrder } from './hooks';

export function CommunityPage() {
  const { params, setParam } = useUrlParams();
  const { draft, setDraft, term: q, clear: clearSearch } = useUrlSearch('busca');
  const order: FeedOrder = params.get('ordem') === 'antigos' ? 'oldest' : 'recent';
  const grid = params.get('visualizacao') === 'grade';
  const feed = useCommunityFeed(q, order);
  const items = feed.data?.pages.flatMap((page) => page.data) ?? [];

  return (
    <div>
      <header className="flex items-center gap-5 rounded-2xl border border-blue-200/70 bg-gradient-to-r from-[#dfeafb] via-[#eaf2fc] to-white px-6 py-7 sm:px-8">
        <span aria-hidden className="hidden size-16 shrink-0 place-items-center rounded-2xl bg-white/70 text-action sm:grid">
          <Users className="size-8" />
        </span>
        <div className="min-w-0">
          <h1 className="font-display text-4xl font-bold text-foreground">Comunidade</h1>
          <p className="mt-2 text-lg text-muted-foreground">Veja o que a comunidade de Ciência da Computação tem compartilhado.</p>
        </div>
      </header>

      <div className="mt-8 grid gap-8 xl:grid-cols-[minmax(0,1fr)_320px]">
        <section aria-labelledby="mural" className="min-w-0">
          <div className="flex flex-wrap items-center gap-3">
            <div className="relative min-w-0 flex-[1_1_16rem]">
              <label htmlFor="busca-comunidade" className="sr-only">
                Buscar na comunidade
              </label>
              <Search className="pointer-events-none absolute top-1/2 left-3.5 size-5 -translate-y-1/2 text-muted-foreground" aria-hidden />
              <input
                id="busca-comunidade"
                type="search"
                value={draft}
                maxLength={100}
                onChange={(event) => setDraft(event.target.value)}
                placeholder="Buscar na comunidade…"
                className="h-11 w-full rounded-lg border border-input bg-white pr-3 pl-11 text-base text-foreground placeholder:text-muted-foreground"
              />
            </div>
            <label htmlFor="ordem-comunidade" className="sr-only">
              Ordenar
            </label>
            <select
              id="ordem-comunidade"
              value={order}
              onChange={(event) => setParam('ordem', event.target.value === 'oldest' ? 'antigos' : null)}
              className="h-11 rounded-lg border border-input bg-white px-3 text-[15px] text-foreground"
            >
              <option value="recent">Mais recentes</option>
              <option value="oldest">Mais antigos</option>
            </select>
            <div role="group" aria-label="Visualização" className="hidden rounded-lg border border-input bg-white p-1 md:flex">
              {[
                { value: null, label: 'Ver em lista', icon: List, active: !grid },
                { value: 'grade', label: 'Ver em grade', icon: LayoutGrid, active: grid },
              ].map(({ value, label, icon: Icon, active }) => (
                <button
                  key={label}
                  type="button"
                  aria-label={label}
                  aria-pressed={active}
                  onClick={() => setParam('visualizacao', value)}
                  className={cn('grid size-9 place-items-center rounded-md text-muted-foreground', active && 'bg-[#e3eefb] text-action')}
                >
                  <Icon className="size-5" aria-hidden />
                </button>
              ))}
            </div>
          </div>

          <h2 id="mural" className="mt-7 font-display text-xl font-bold text-foreground">
            O que está rolando
          </h2>
          <p className="mt-1 text-[15px] text-muted-foreground">
            {order === 'oldest' ? 'Atualizações mais antigas primeiro.' : 'Atualizações mais recentes da comunidade.'}
          </p>

          <div className="mt-5 space-y-4">
            {feed.isPending && (
              <>
                <span role="status" className="sr-only">
                  Carregando novidades…
                </span>
                <Skeleton className="h-28 rounded-2xl" />
                <Skeleton className="h-28 rounded-2xl" />
                <Skeleton className="h-28 rounded-2xl" />
              </>
            )}

            {/* também com itens na tela ("Mostrar mais" que falhou): o aviso aparece e os itens ficam */}
            {feed.isError && (
              <FormAlert
                variant="warning"
                action={
                  <button
                    type="button"
                    onClick={() => (feed.isFetchNextPageError ? feed.fetchNextPage() : feed.refetch())}
                    className="font-semibold underline underline-offset-2"
                  >
                    Tentar novamente
                  </button>
                }
              >
                Não foi possível carregar as novidades agora.
              </FormAlert>
            )}

            {feed.data && items.length === 0 && q && (
              <div role="status" className="rounded-2xl border border-dashed border-glow bg-white p-7">
                <h3 className="font-display text-lg font-bold [overflow-wrap:anywhere] text-foreground">Nenhum resultado para “{q}”</h3>
                <p className="mt-2 text-base text-muted-foreground">Tente outro título, disciplina ou nome.</p>
                <button type="button" onClick={clearSearch} className="mt-4 font-semibold text-action underline-offset-2 hover:underline">
                  Limpar busca
                </button>
              </div>
            )}

            {feed.data && items.length === 0 && !q && (
              <div className="rounded-2xl border border-dashed border-glow bg-white p-7">
                <h3 className="font-display text-lg font-bold text-foreground">Ainda não há novidades compartilhadas</h3>
                <p className="mt-2 text-base text-muted-foreground">Quando um material for aprovado em Estudos, ele aparece aqui.</p>
                <Link to="/estudos" className="mt-4 inline-block font-semibold text-action underline-offset-2 hover:underline">
                  Ir para Estudos
                </Link>
              </div>
            )}

            {items.length > 0 && (
              <ul className={cn(grid ? 'grid gap-4 md:grid-cols-2' : 'space-y-4')}>
                {items.map((item) => (
                  <li key={item.id}>
                    <FeedEntry item={item} grid={grid} />
                  </li>
                ))}
              </ul>
            )}

            {feed.hasNextPage && !feed.isFetchNextPageError && (
              <div className="flex justify-center">
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => feed.fetchNextPage()}
                  disabled={feed.isFetchingNextPage}
                  className="h-11 border-action px-5 text-[15px] font-semibold text-action"
                >
                  {feed.isFetchingNextPage && <Loader2 className="size-5 animate-spin" aria-hidden />}
                  Mostrar mais
                </Button>
              </div>
            )}
          </div>
        </section>

        <CommunityAside />
      </div>
    </div>
  );
}
