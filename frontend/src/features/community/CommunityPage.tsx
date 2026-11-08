import { Loader2 } from 'lucide-react';
import { Link } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { Skeleton } from '@/components/Skeleton';
import { Button } from '@/components/ui/button';
import { FeedEntry } from './FeedEntry';
import { useCommunityFeed } from './hooks';

export function CommunityPage() {
  const feed = useCommunityFeed();
  const items = feed.data?.pages.flatMap((page) => page.data) ?? [];

  return (
    <div>
      <header>
        <h1 className="font-display text-4xl font-bold text-foreground">Comunidade</h1>
        <p className="mt-2 text-lg text-muted-foreground">Veja o que a comunidade de Ciência da Computação tem compartilhado.</p>
      </header>

      <section aria-labelledby="mural" className="mt-8">
        <h2 id="mural" className="font-display text-xl font-bold text-foreground">
          O que está rolando
        </h2>
        <p className="mt-1 text-[15px] text-muted-foreground">Mais recentes primeiro.</p>

        <div className="mt-5 space-y-4">
          {feed.isPending && (
            <>
              <span role="status" className="sr-only">
                Carregando novidades…
              </span>
              <Skeleton className="h-24 rounded-2xl" />
              <Skeleton className="h-24 rounded-2xl" />
              <Skeleton className="h-24 rounded-2xl" />
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

          {feed.data && items.length === 0 && (
            <div className="rounded-2xl border border-dashed border-glow bg-white p-7">
              <h3 className="font-display text-lg font-bold text-foreground">Ainda não há novidades compartilhadas</h3>
              <p className="mt-2 text-base text-muted-foreground">Quando um material for aprovado em Estudos, ele aparece aqui.</p>
              <Link to="/estudos" className="mt-4 inline-block font-semibold text-action underline-offset-2 hover:underline">
                Ir para Estudos
              </Link>
            </div>
          )}

          {items.length > 0 && (
            <ul className="space-y-4">
              {items.map((item) => (
                <li key={item.id}>
                  <FeedEntry item={item} />
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
    </div>
  );
}
