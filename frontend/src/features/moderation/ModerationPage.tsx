import { useRef, useState } from 'react';
import { Chip } from '@/components/Chip';
import { FormAlert } from '@/components/FormAlert';
import { Skeleton } from '@/components/Skeleton';
import { StatusTag } from '@/components/StatusTag';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { timeAgo } from '@/lib/format';
import { DecisionDialog, type DecisionKind } from './DecisionDialog';
import { type ModerationMaterial, useModerationQueue } from './hooks';
import { ReviewSheet } from './ReviewSheet';

type Notice = { title: string; detail?: string };

const CANCELLED = 'material_cancelled';

export function ModerationPage() {
  const queue = useModerationQueue();
  const [selected, setSelected] = useState<ModerationMaterial | null>(null);
  const [decision, setDecision] = useState<DecisionKind | null>(null);
  const [conflict, setConflict] = useState<string | null>(null);
  const [notice, setNotice] = useState<Notice | null>(null);
  const items = queue.data ?? [];
  const noticeRef = useRef<HTMLDivElement>(null);
  const headingRef = useRef<HTMLHeadingElement>(null);
  // o botão "Analisar" some depois da decisão; o foco vai para o aviso (ou para o título, após conflito)
  const focusTarget = useRef<'notice' | 'heading' | null>(null);

  function restoreFocus(event: Event) {
    if (!focusTarget.current) return;
    event.preventDefault();
    (focusTarget.current === 'notice' ? noticeRef : headingRef).current?.focus();
  }

  function analyze(material: ModerationMaterial) {
    focusTarget.current = null;
    setSelected(material);
  }

  function done(kind: DecisionKind) {
    if (!selected) return;
    focusTarget.current = 'notice';
    setNotice(
      kind === 'approve'
        ? {
            title: 'Conteúdo aprovado com sucesso.',
            detail: `${
              selected.revision_of
                ? `A alteração passa a valer em Estudos › ${selected.discipline.name}.`
                : `O material passa a aparecer em Estudos › ${selected.discipline.name}.`
            } O autor será notificado.`,
          }
        : { title: 'Conteúdo rejeitado.', detail: 'O autor será notificado.' },
    );
    setDecision(null);
    setSelected(null);
  }

  function refreshAfterConflict() {
    focusTarget.current = 'heading';
    setConflict(null);
    setSelected(null);
    queue.refetch();
  }

  return (
    <div>
      <header>
        <h1 ref={headingRef} tabIndex={-1} className="font-display text-4xl font-bold text-foreground outline-none">
          Moderação
        </h1>
        <p className="mt-2 text-lg text-muted-foreground">Analise conteúdos enviados pela comunidade antes da publicação.</p>
      </header>

      <div className="mt-8 space-y-6">
        {notice && (
          <div ref={noticeRef} tabIndex={-1} className="outline-none">
            <FormAlert variant="success" onDismiss={() => setNotice(null)}>
              <strong>{notice.title}</strong> {notice.detail}
            </FormAlert>
          </div>
        )}

        {queue.isPending && (
          <div className="space-y-4" aria-busy="true">
            <p className="text-base text-muted-foreground">Carregando conteúdos pendentes…</p>
            <Skeleton className="h-32 rounded-2xl" />
            <Skeleton className="h-32 rounded-2xl" />
          </div>
        )}

        {queue.isError && !queue.data && (
          <FormAlert
            variant="warning"
            action={
              <button type="button" onClick={() => queue.refetch()} className="font-semibold underline underline-offset-2">
                Tentar novamente
              </button>
            }
          >
            Não foi possível carregar os conteúdos pendentes agora.
          </FormAlert>
        )}

        {queue.data && items.length === 0 && (
          <div className="rounded-2xl border border-blue-200/70 bg-white p-7">
            <h2 className="font-display text-xl font-bold text-foreground">Tudo certo por aqui</h2>
            <p className="mt-2 text-base text-muted-foreground">Não há conteúdos aguardando análise no momento.</p>
          </div>
        )}

        {items.length > 0 && (
          <section aria-labelledby="fila-pendentes">
            <h2 id="fila-pendentes" aria-live="polite" className="font-display text-2xl font-bold text-foreground">
              {items.length === 1 ? '1 conteúdo pendente' : `${items.length} conteúdos pendentes`}
            </h2>
            <p className="mt-1 text-[15px] text-muted-foreground">Mais antigos primeiro.</p>
            <ul className="mt-5 space-y-4">
              {items.map((material) => (
                <li key={material.id} className="rounded-2xl border border-blue-200/70 bg-white p-6">
                  <p className="flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                    <span>Tipo: Material</span>
                    <span aria-hidden>·</span>
                    <span className="inline-flex items-center gap-2">
                      Status: <StatusTag status="PENDENTE" label="Pendente" />
                    </span>
                    {material.revision_of && <StatusTag status="ALTERACAO" />}
                    {material.is_own && <Chip>Seu envio</Chip>}
                  </p>
                  <h3 className="mt-2 font-display text-lg font-bold [overflow-wrap:anywhere] text-foreground">{material.title}</h3>
                  <p className="mt-1 text-[15px] text-muted-foreground">
                    Enviado por {material.author_name ?? 'Comunidade'} · {timeAgo(material.submitted_at)} · Disciplina: {material.discipline.name}
                  </p>
                  <Button type="button" onClick={() => analyze(material)} className="mt-4 h-11 px-5 text-[15px] font-semibold">
                    Analisar
                  </Button>
                </li>
              ))}
            </ul>
          </section>
        )}
      </div>

      <ReviewSheet
        material={selected}
        locked={decision !== null}
        onClose={() => setSelected(null)}
        onDecide={setDecision}
        onCloseAutoFocus={restoreFocus}
      />

      {selected && (
        <DecisionDialog
          kind={decision}
          material={selected}
          onCancel={() => setDecision(null)}
          onDone={done}
          onCloseAutoFocus={restoreFocus}
          onConflict={(code) => {
            setDecision(null);
            setConflict(code ?? 'already_decided');
          }}
        />
      )}

      <Dialog open={conflict !== null} onOpenChange={(next) => !next && refreshAfterConflict()}>
        <DialogContent showCloseButton={false} onCloseAutoFocus={restoreFocus} className="w-[calc(100%-2rem)] max-w-md gap-0 p-6 sm:max-w-md">
          <DialogTitle className="font-display text-xl font-bold text-foreground">
            {conflict === CANCELLED ? 'Este conteúdo foi cancelado pelo autor' : 'Este conteúdo já foi analisado'}
          </DialogTitle>
          <DialogDescription className="mt-2 text-[15px] text-muted-foreground">
            O status deste conteúdo foi atualizado por outra ação. Atualize a fila para continuar.
          </DialogDescription>
          <div className="mt-6 flex justify-end">
            <Button type="button" onClick={refreshAfterConflict} className="h-11 px-5 text-[15px] font-semibold">
              Atualizar fila
            </Button>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
