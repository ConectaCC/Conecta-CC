import { ExternalLink, X } from 'lucide-react';
import { Chip } from '@/components/Chip';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetDescription, SheetTitle } from '@/components/ui/sheet';
import { formatDateTime, hostOf, timeAgo } from '@/lib/format';
import type { ModerationMaterial } from './hooks';

type Props = {
  material: ModerationMaterial | null;
  locked: boolean;
  onClose: () => void;
  onDecide: (kind: 'approve' | 'reject') => void;
};

export function ReviewSheet({ material, locked, onClose, onDecide }: Props) {
  return (
    <Sheet open={material !== null} onOpenChange={(next) => !next && !locked && onClose()}>
      <SheetContent
        showCloseButton={false}
        className="gap-0 overflow-y-auto p-6 data-[side=right]:w-full data-[side=right]:sm:max-w-lg"
      >
        {material && <ReviewContent material={material} onClose={onClose} onDecide={onDecide} />}
      </SheetContent>
    </Sheet>
  );
}

function ReviewContent({ material, onClose, onDecide }: { material: ModerationMaterial; onClose: () => void; onDecide: Props['onDecide'] }) {
  const url = new URL(material.url);
  const host = hostOf(material.url);

  return (
    <>
      <div className="flex items-start justify-between gap-3">
        <SheetTitle className="font-display text-xl font-bold text-foreground">Analisar conteúdo</SheetTitle>
        <Button type="button" variant="ghost" size="icon-sm" onClick={onClose} aria-label="Fechar">
          <X className="size-5" aria-hidden />
        </Button>
      </div>
      <SheetDescription className="sr-only">Dados enviados para análise da moderação.</SheetDescription>

      <p className="mt-4 flex flex-wrap gap-2 text-sm text-muted-foreground">
        <span>Tipo: Material</span>
        <span aria-hidden>·</span>
        <span>Status: Pendente</span>
        {material.is_own && <Chip>Seu envio</Chip>}
      </p>
      <h3 className="mt-3 font-display text-lg font-bold [overflow-wrap:anywhere] text-foreground">{material.title}</h3>

      <dl className="mt-5 space-y-4 text-[15px]">
        <div>
          <dt className="font-semibold text-foreground">Autor</dt>
          <dd className="mt-1 flex flex-wrap items-center gap-2 text-foreground">
            {material.author_name ?? 'Comunidade'}
            {material.anonymous && material.author_name && <Chip>Enviado anonimamente</Chip>}
          </dd>
          <dd className="mt-1 text-sm text-muted-foreground">Visível apenas para a moderação.</dd>
        </div>
        <div>
          <dt className="font-semibold text-foreground">Enviado em</dt>
          <dd className="mt-1 text-foreground">
            {formatDateTime(material.submitted_at)} ({timeAgo(material.submitted_at)})
          </dd>
        </div>
        <div>
          <dt className="font-semibold text-foreground">Disciplina</dt>
          <dd className="mt-1 text-foreground">{material.discipline.name}</dd>
        </div>
        {material.description && (
          <div>
            <dt className="font-semibold text-foreground">Descrição</dt>
            <dd className="mt-1 [overflow-wrap:anywhere] text-foreground">{material.description}</dd>
          </div>
        )}
        <div>
          <dt className="font-semibold text-foreground">Link enviado</dt>
          <dd className="mt-1 break-all text-foreground">
            <span className="font-semibold">{host}</span>
            <span className="text-muted-foreground">{url.pathname + url.search + url.hash}</span>
          </dd>
          <dd className="mt-2">
            <a
              href={material.url}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center gap-2 font-semibold text-action hover:underline"
            >
              Abrir link enviado
              <ExternalLink className="size-4" aria-hidden />
              <span className="sr-only">(abre em nova guia, {host})</span>
            </a>
          </dd>
        </div>
      </dl>

      <p className="mt-5 rounded-lg bg-ice px-4 py-3 text-[15px] text-foreground">
        Se aprovado, aparecerá em Estudos › {material.discipline.name}
      </p>

      <div className="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <Button
          type="button"
          variant="outline"
          onClick={() => onDecide('reject')}
          className="h-11 border-danger px-5 text-[15px] font-semibold text-danger hover:bg-danger-bg"
        >
          Rejeitar
        </Button>
        <Button type="button" onClick={() => onDecide('approve')} className="h-11 px-5 text-[15px] font-semibold">
          Aprovar
        </Button>
      </div>
    </>
  );
}
