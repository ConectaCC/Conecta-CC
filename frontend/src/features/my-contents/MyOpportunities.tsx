import { Loader2 } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { useSearchParams } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { Button } from '@/components/ui/button';
import { ApiError } from '@/lib/api/errors';
import { ConfirmActionDialog } from './ConfirmActionDialog';
import { FILTERS, type MyFilter, type MyOpportunity, useCancelOpportunity, useDeleteOpportunity, useMyOpportunities } from './hooks';
import { StatusFilters } from './StatusFilters';
import { OpportunityItem } from './OpportunityItem';

type Action = { kind: 'cancel' | 'delete'; item: MyOpportunity };

const GENERIC_ERROR = 'Não foi possível concluir a ação. Tente novamente.';

const COPY = {
  cancel: {
    title: 'Cancelar envio?',
    description: 'A oportunidade sai da fila de análise e não será publicada.',
    confirm: 'Cancelar envio',
    busy: 'Cancelando…',
    back: 'Voltar',
    done: 'Envio cancelado.',
  },
  delete: {
    title: 'Excluir oportunidade?',
    description: 'A oportunidade deixará de aparecer na página e no mural. Você ainda poderá consultá-la em Meus conteúdos.',
    confirm: 'Excluir oportunidade',
    busy: 'Excluindo…',
    back: 'Cancelar',
    done: 'Oportunidade excluída.',
  },
};

export function MyOpportunities() {
  const [params, setParams] = useSearchParams();
  const filterParam = params.get('filtro');
  const filter: MyFilter = FILTERS.some((item) => item.value === filterParam) ? (filterParam as MyFilter) : 'all';
  const target = params.get('item');
  const list = useMyOpportunities(filter);
  const cancel = useCancelOpportunity();
  const remove = useDeleteOpportunity();
  const [action, setAction] = useState<Action | null>(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [actionError, setActionError] = useState<string | null>(null);
  const [notice, setNotice] = useState<string | null>(null);
  const noticeRef = useRef<HTMLDivElement>(null);
  const focusNotice = useRef(false);
  const scrolled = useRef(false);
  const items = list.data?.pages.flatMap((page) => page.data) ?? [];
  const found = target !== null && items.some((item) => item.code === target);

  // vindo de uma notificação (?item=OPO-…): carrega até achar e rola até o item
  useEffect(() => {
    if (!target || found || !list.hasNextPage || list.isFetchingNextPage || list.isFetchNextPageError) return;
    void list.fetchNextPage();
  }, [target, found, list]);

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

  function ask(kind: Action['kind'], item: MyOpportunity) {
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
  const retry = (
    <button type="button" onClick={() => (list.isFetchNextPageError ? list.fetchNextPage() : list.refetch())} className="font-semibold underline underline-offset-2">
      Tentar novamente
    </button>
  );

  return (
    <div>
      <StatusFilters value={filter} onChange={selectFilter} />

      <div className="mt-6 space-y-4">
        {notice && (
          <div ref={noticeRef} tabIndex={-1} className="outline-none">
            <FormAlert variant="success" onDismiss={() => setNotice(null)}>
              {notice}
            </FormAlert>
          </div>
        )}

        {list.isPending && (
          <p aria-busy="true" className="text-base text-muted-foreground">
            Carregando suas oportunidades…
          </p>
        )}

        {list.isError && (
          <FormAlert variant="warning" action={retry}>
            Não foi possível carregar suas oportunidades agora.
          </FormAlert>
        )}

        {list.data && items.length === 0 && (
          <p className="rounded-2xl border border-blue-200/70 bg-white p-7 text-base text-muted-foreground">
            {filter === 'all' ? 'Você ainda não enviou oportunidades.' : 'Nenhuma oportunidade neste filtro.'}
          </p>
        )}

        {items.length > 0 && (
          <ul className="space-y-4">
            {items.map((item) => (
              <OpportunityItem key={item.id} item={item} onCancel={() => ask('cancel', item)} onDelete={() => ask('delete', item)} />
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
