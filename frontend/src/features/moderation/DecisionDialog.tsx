import { CircleAlert, Loader2 } from 'lucide-react';
import { useId, useState } from 'react';
import { FormAlert } from '@/components/FormAlert';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { ApiError } from '@/lib/api/errors';
import { cn } from '@/lib/utils';
import { type QueueItem, useApprove, useReject } from './hooks';

export type DecisionKind = 'approve' | 'reject';

type Props = {
  kind: DecisionKind | null;
  queued: QueueItem;
  onCancel: () => void;
  onDone: (kind: DecisionKind) => void;
  onConflict: (code: string | undefined) => void;
  onCloseAutoFocus?: (event: Event) => void;
};

const REQUIRED_REASON = 'Informe o motivo da rejeição.';

export function DecisionDialog({ kind, queued, onCancel, onDone, onConflict, onCloseAutoFocus }: Props) {
  const approve = useApprove();
  const reject = useReject();
  const title = queued.item.title;
  const [reason, setReason] = useState('');
  const [reasonError, setReasonError] = useState<string | null>(null);
  const [failed, setFailed] = useState<string | null>(null);
  const id = useId();
  const busy = approve.isPending || reject.isPending;
  const isReject = kind === 'reject';

  function reset() {
    setReason('');
    setReasonError(null);
    setFailed(null);
  }

  function cancel() {
    if (busy) return;
    reset();
    onCancel();
  }

  async function confirm() {
    if (busy || !kind) return;
    setFailed(null);
    if (isReject && reason.trim() === '') {
      setReasonError(REQUIRED_REASON);
      return;
    }
    try {
      if (isReject) await reject.mutateAsync({ queued, reason });
      else await approve.mutateAsync(queued);
      reset();
      onDone(kind);
    } catch (error) {
      if (error instanceof ApiError && error.code === 'link_taken') {
        // a alteração continua pendente: o moderador pode rejeitar com o motivo
        setFailed(error.message);
      } else if (error instanceof ApiError && error.status === 409) {
        reset();
        onConflict(error.code);
      } else if (error instanceof ApiError && error.status === 422 && error.fieldErrors.reason) {
        setReasonError(error.fieldErrors.reason[0] ?? null);
      } else {
        setFailed(isReject ? 'Não foi possível rejeitar o conteúdo. Tente novamente.' : 'Não foi possível aprovar o conteúdo. Tente novamente.');
      }
    }
  }

  const reasonId = `${id}-reason`;

  return (
    <Dialog open={kind !== null} onOpenChange={(next) => !next && cancel()}>
      <DialogContent showCloseButton={false} onCloseAutoFocus={onCloseAutoFocus} className="w-[calc(100%-2rem)] max-w-md gap-0 p-6 sm:max-w-md">
        <DialogTitle className="font-display text-xl font-bold text-foreground">{isReject ? 'Rejeitar conteúdo?' : 'Aprovar conteúdo?'}</DialogTitle>
        {isReject ? (
          <DialogDescription className="mt-2 text-[15px] font-semibold [overflow-wrap:anywhere] text-foreground">{title}</DialogDescription>
        ) : (
          <>
            <p className="mt-2 text-[15px] font-semibold [overflow-wrap:anywhere] text-foreground">{title}</p>
            <DialogDescription className="mt-2 text-[15px] text-muted-foreground">
              Depois da aprovação, este conteúdo ficará disponível na área correspondente do ConectaCC.
            </DialogDescription>
          </>
        )}

        {isReject && (
          <div className="mt-4 space-y-2">
            <Label htmlFor={reasonId} className="text-[15px] font-medium text-foreground">
              Motivo da rejeição *
            </Label>
            <textarea
              id={reasonId}
              rows={3}
              value={reason}
              disabled={busy}
              aria-required
              aria-invalid={reasonError ? true : undefined}
              aria-describedby={`${reasonId}-hint${reasonError ? ` ${reasonId}-error` : ''}`}
              onChange={(event) => {
                setReason(event.target.value);
                setReasonError(null);
              }}
              className={cn(
                'w-full rounded-lg border border-input bg-white px-4 py-3 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50',
                reasonError && 'border-danger',
              )}
            />
            {reasonError && (
              <p id={`${reasonId}-error`} className="flex items-start gap-2 text-sm text-danger">
                <CircleAlert className="mt-0.5 size-4 shrink-0" aria-hidden />
                <span>{reasonError}</span>
              </p>
            )}
            <p id={`${reasonId}-hint`} className="text-sm text-muted-foreground">
              O estudante verá o motivo em Meus conteúdos.
            </p>
          </div>
        )}

        {failed && (
          <div className="mt-4">
            <FormAlert variant="danger">{failed}</FormAlert>
          </div>
        )}

        <div className="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
          <Button type="button" variant="outline" onClick={cancel} disabled={busy} className="h-11 border-action px-5 text-[15px] font-semibold text-action">
            Cancelar
          </Button>
          <Button
            type="button"
            onClick={confirm}
            disabled={busy}
            className={cn('h-11 px-5 text-[15px] font-semibold', isReject && 'bg-danger text-white hover:bg-danger/90')}
          >
            {busy && <Loader2 className="size-5 animate-spin" aria-hidden />}
            {isReject ? (busy ? 'Rejeitando…' : 'Rejeitar conteúdo') : busy ? 'Aprovando…' : 'Aprovar conteúdo'}
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
