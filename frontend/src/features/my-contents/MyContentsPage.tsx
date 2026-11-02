import { Loader2 } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { Link, useSearchParams } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { Button } from '@/components/ui/button';
import { ShareMaterialDialog } from '@/features/materials/ShareMaterialDialog';
import { ApiError } from '@/lib/api/errors';
import { cn } from '@/lib/utils';
import { ConfirmActionDialog } from './ConfirmActionDialog';
import { ContentItem } from './ContentItem';
import { FILTERS, type MyFilter, type MyMaterial, useCancelMaterial, useDeleteMaterial, useMyMaterials } from './hooks';

type Pending = { kind: 'cancel' | 'delete'; item: MyMaterial } | null;

const GENERIC_ERROR = 'Não foi possível concluir a ação. Tente novamente.';

function isFilter(value: string | null): value is MyFilter {
  return FILTERS.some((filter) => filter.value === value);
}

export function MyContentsPage() {
  const [params, setParams] = useSearchParams();
  const filterParam = params.get('filtro');
  const filter: MyFilter = isFilter(filterParam) ? filterParam : 'all';
  const target = params.get('item');
  const materials = useMyMaterials(filter);
  const cancel = useCancelMaterial();
  const remove = useDeleteMaterial();
  const [expanded, setExpanded] = useState<Set<string>>(() => new Set(target ? [target] : []));
  const [pending, setPending] = useState<Pending>(null);
  const [editing, setEditing] = useState<MyMaterial | null>(null);
  const [actionError, setActionError] = useState<string | null>(null);
  const scrolled = useRef(false);

  const items = materials.data?.pages.flatMap((page) => page.data) ?? [];
  const found = target !== null && items.some((item) => item.code === target);

  // ?item= vindo de uma notificação: carrega as próximas páginas até achar o item
  useEffect(() => {
    if (!target || found || !materials.hasNextPage || materials.isFetchingNextPage || materials.isFetchNextPageError) return;
    materials.fetchNextPage();
  }, [target, found, materials]);

  useEffect(() => {
    if (!found || scrolled.current) return;
    scrolled.current = true;
    document.getElementById(`item-${target}`)?.scrollIntoView({ block: 'start' });
  }, [found, target]);

  function selectFilter(next: MyFilter) {
    const nextParams = new URLSearchParams(params);
    if (next === 'all') nextParams.delete('filtro');
    else nextParams.set('filtro', next);
    setParams(nextParams, { replace: true });
  }

  function toggle(code: string) {
    setExpanded((current) => {
      const next = new Set(current);
      if (next.has(code)) next.delete(code);
      else next.add(code);
      return next;
    });
  }

  async function confirm() {
    if (!pending) return;
    setActionError(null);
    try {
      if (pending.kind === 'cancel') await cancel.mutateAsync(pending.item);
      else await remove.mutateAsync(pending.item);
      setPending(null);
    } catch (error) {
      setActionError(error instanceof ApiError && error.status === 409 ? error.message : GENERIC_ERROR);
    }
  }

  function closeDialog() {
    setPending(null);
    setActionError(null);
  }

  const retry = (
    <button
      type="button"
      onClick={() => (materials.isFetchNextPageError ? materials.fetchNextPage() : materials.refetch())}
      className="font-semibold underline underline-offset-2"
    >
      Tentar novamente
    </button>
  );

  return (
    <div>
      <nav aria-label="Trilha" className="text-[15px] text-muted-foreground">
        <Link to="/perfil" className="font-semibold text-action hover:underline">
          Perfil
        </Link>
        <span aria-hidden className="mx-2">
          /
        </span>
        <span aria-current="page">Meus conteúdos</span>
      </nav>
      <header className="mt-4">
        <h1 className="font-display text-4xl font-bold text-foreground">Meus conteúdos</h1>
        <p className="mt-2 text-lg text-muted-foreground">Acompanhe o que você enviou e a análise da moderação.</p>
      </header>

      <div role="group" aria-label="Filtrar por situação" className="mt-6 flex flex-wrap gap-2">
        {FILTERS.map(({ value, label }) => (
          <button
            key={value}
            type="button"
            aria-pressed={filter === value}
            onClick={() => selectFilter(value)}
            className={cn(
              'h-10 rounded-full border px-4 text-[15px] font-medium',
              filter === value ? 'border-navy-900 bg-navy-900 text-white' : 'border-blue-200 bg-white text-foreground hover:bg-ice',
            )}
          >
            {label}
          </button>
        ))}
      </div>

      <div className="mt-6 space-y-4">
        {materials.isPending && (
          <p aria-busy="true" className="text-base text-muted-foreground">
            Carregando seus conteúdos…
          </p>
        )}

        {materials.isError && !materials.isFetchNextPageError && !materials.data && (
          <FormAlert variant="warning" action={retry}>
            Não foi possível carregar seus conteúdos agora.
          </FormAlert>
        )}

        {materials.data && items.length === 0 && (
          <p className="rounded-2xl border border-blue-200/70 bg-white p-7 text-base text-muted-foreground">
            {filter === 'all' ? 'Você ainda não enviou conteúdos.' : 'Nenhum conteúdo neste filtro.'}
          </p>
        )}

        {items.length > 0 && (
          <ul className="space-y-4">
            {items.map((item) => (
              <ContentItem
                key={item.id}
                item={item}
                expanded={expanded.has(item.code)}
                onToggle={() => toggle(item.code)}
                onCancel={() => setPending({ kind: 'cancel', item })}
                onDelete={() => setPending({ kind: 'delete', item })}
                onEdit={() => setEditing(item)}
              />
            ))}
          </ul>
        )}

        {materials.isFetchNextPageError && (
          <FormAlert variant="warning" action={retry}>
            Não foi possível carregar seus conteúdos agora.
          </FormAlert>
        )}

        {materials.hasNextPage && !materials.isFetchNextPageError && (
          <div className="flex justify-center">
            <Button
              type="button"
              variant="outline"
              onClick={() => materials.fetchNextPage()}
              disabled={materials.isFetchingNextPage}
              className="h-11 border-action px-5 text-[15px] font-semibold text-action"
            >
              {materials.isFetchingNextPage && <Loader2 className="size-5 animate-spin" aria-hidden />}
              Mostrar mais
            </Button>
          </div>
        )}
      </div>

      {editing && (
        <ShareMaterialDialog
          discipline={editing.discipline}
          open
          onOpenChange={(open) => !open && setEditing(null)}
          editing={{ id: editing.id, title: editing.title, description: editing.description, url: editing.url }}
        />
      )}

      <ConfirmActionDialog
        open={pending !== null}
        title={pending?.kind === 'delete' ? 'Excluir material?' : 'Cancelar envio?'}
        description={
          pending?.kind === 'delete'
            ? 'O material deixará de aparecer na disciplina. Você ainda poderá consultá-lo em Meus conteúdos.'
            : 'O material sai da fila de análise e não será publicado.'
        }
        confirmLabel={pending?.kind === 'delete' ? 'Excluir material' : 'Cancelar envio'}
        busyLabel={pending?.kind === 'delete' ? 'Excluindo…' : 'Cancelando…'}
        cancelLabel={pending?.kind === 'delete' ? 'Cancelar' : 'Voltar'}
        busy={cancel.isPending || remove.isPending}
        error={actionError}
        onConfirm={confirm}
        onCancel={closeDialog}
      />
    </div>
  );
}
