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

type Action = { kind: 'cancel' | 'delete'; item: MyMaterial };

const GENERIC_ERROR = 'Não foi possível concluir a ação. Tente novamente.';

const COPY = {
  cancel: {
    title: 'Cancelar envio?',
    description: 'O material sai da fila de análise e não será publicado.',
    confirm: 'Cancelar envio',
    busy: 'Cancelando…',
    back: 'Voltar',
    done: 'Envio cancelado.',
  },
  delete: {
    title: 'Excluir material?',
    description: 'O material deixará de aparecer na disciplina. Você ainda poderá consultá-lo em Meus conteúdos.',
    confirm: 'Excluir material',
    busy: 'Excluindo…',
    back: 'Cancelar',
    done: 'Material excluído.',
  },
};

function isFilter(value: string | null): value is MyFilter {
  return FILTERS.some((filter) => filter.value === value);
}

// ?item= vindo de uma notificação: a lista remonta quando o item muda, para abrir e rolar até o novo
export function MyContentsPage() {
  const [params] = useSearchParams();
  return <MyContents key={params.get('item') ?? ''} />;
}

function MyContents() {
  const [params, setParams] = useSearchParams();
  const filterParam = params.get('filtro');
  const filter: MyFilter = isFilter(filterParam) ? filterParam : 'all';
  const target = params.get('item');
  const materials = useMyMaterials(filter);
  const cancel = useCancelMaterial();
  const remove = useDeleteMaterial();
  const [expanded, setExpanded] = useState<Set<string>>(() => new Set(target ? [target] : []));
  // o texto do diálogo fica com a última ação até a animação de saída terminar
  const [action, setAction] = useState<Action | null>(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [actionError, setActionError] = useState<string | null>(null);
  const [editing, setEditing] = useState<MyMaterial | null>(null);
  const [notice, setNotice] = useState<string | null>(null);
  const noticeRef = useRef<HTMLDivElement>(null);
  const focusNotice = useRef(false);
  const scrolled = useRef(false);

  const items = materials.data?.pages.flatMap((page) => page.data) ?? [];
  const found = target !== null && items.some((item) => item.code === target);

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

  function ask(kind: Action['kind'], item: MyMaterial) {
    setAction({ kind, item });
    setActionError(null);
    setDialogOpen(true);
  }

  async function confirm() {
    if (!action) return;
    setActionError(null);
    try {
      if (action.kind === 'cancel') await cancel.mutateAsync(action.item);
      else await remove.mutateAsync(action.item);
      setNotice(COPY[action.kind].done);
      // o botão clicado some depois da ação: o foco vai para o aviso
      focusNotice.current = true;
      setDialogOpen(false);
    } catch (error) {
      setActionError(error instanceof ApiError && error.status === 409 ? error.message : GENERIC_ERROR);
    }
  }

  function restoreFocus(event: Event) {
    if (!focusNotice.current) return;
    event.preventDefault();
    focusNotice.current = false;
    noticeRef.current?.focus();
  }

  const copy = COPY[action?.kind ?? 'cancel'];
  const refetchFailed = materials.isError && !materials.isFetchNextPageError;

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
        {notice && (
          <div ref={noticeRef} tabIndex={-1} className="outline-none">
            <FormAlert variant="success" onDismiss={() => setNotice(null)}>
              {notice}
            </FormAlert>
          </div>
        )}

        {materials.isPending && (
          <p aria-busy="true" className="text-base text-muted-foreground">
            Carregando seus conteúdos…
          </p>
        )}

        {/* também quando já há dados na tela: o aviso fica acima e a lista continua */}
        {refetchFailed && (
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
                onCancel={() => ask('cancel', item)}
                onDelete={() => ask('delete', item)}
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
        open={dialogOpen}
        title={copy.title}
        description={copy.description}
        confirmLabel={copy.confirm}
        busyLabel={copy.busy}
        cancelLabel={copy.back}
        busy={cancel.isPending || remove.isPending}
        error={actionError}
        onConfirm={confirm}
        onCancel={() => setDialogOpen(false)}
        onCloseAutoFocus={restoreFocus}
      />
    </div>
  );
}
