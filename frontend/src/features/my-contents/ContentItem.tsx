import { ChevronDown, ExternalLink } from 'lucide-react';
import { useId } from 'react';
import { Link } from 'react-router';
import { Chip } from '@/components/Chip';
import { StatusTag } from '@/components/StatusTag';
import { Button } from '@/components/ui/button';
import { formatDate, hostOf } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { MyMaterial } from './hooks';

type Props = {
  item: MyMaterial;
  expanded: boolean;
  onToggle: () => void;
  onCancel: () => void;
  onDelete: () => void;
  onEdit?: () => void;
};

const ACTION = 'h-11 border-action px-4 text-[15px] font-semibold text-action';

export function ContentItem({ item, expanded, onToggle, onCancel, onDelete, onEdit }: Props) {
  const panelId = useId();

  return (
    <li id={`item-${item.code}`} className="scroll-mt-24 rounded-2xl border border-blue-200/70 bg-white p-6">
      <div className="flex flex-wrap items-start justify-between gap-3">
        <div className="min-w-0">
          <p className="text-xs font-bold tracking-wide text-muted-foreground uppercase [overflow-wrap:anywhere]">
            {item.code} · Material · Estudos › {item.discipline.name}
          </p>
          <h2 className="mt-1 font-display text-lg font-bold [overflow-wrap:anywhere] text-foreground">{item.title}</h2>
          <p className="mt-1 flex flex-wrap items-center gap-2 text-[15px] text-muted-foreground">
            Enviado em {formatDate(item.submitted_at)}
            {item.anonymous && <Chip>Anônimo</Chip>}
          </p>
        </div>
        <div className="flex flex-wrap items-center gap-2">
          <StatusTag status={item.status} />
          {(item.pending_revision || item.rejected_revision) && <StatusTag status="ALTERACAO" />}
        </div>
      </div>

      {item.pending_revision && (
        <p className="mt-3 text-[15px] text-muted-foreground">Alteração em análise desde {formatDate(item.pending_revision.submitted_at)}</p>
      )}
      {item.rejected_revision && (
        <p className="mt-3 text-[15px] [overflow-wrap:anywhere] text-muted-foreground">
          Alteração não aprovada
          {item.rejected_revision.decided_at && ` em ${formatDate(item.rejected_revision.decided_at)}`}: {item.rejected_revision.reason}
        </p>
      )}
      {item.status === 'REJEITADO' && item.rejection_reason && (
        <p className="mt-3 rounded-lg bg-danger-bg px-4 py-3 text-[15px] [overflow-wrap:anywhere] text-foreground">
          <strong>Motivo da rejeição:</strong> {item.rejection_reason}
        </p>
      )}

      {expanded && (
        <dl id={panelId} className="mt-4 space-y-3 rounded-lg bg-ice px-4 py-3 text-[15px]">
          <div>
            <dt className="font-semibold text-foreground">Título</dt>
            <dd className="[overflow-wrap:anywhere] text-foreground">{item.title}</dd>
          </div>
          {item.description && (
            <div>
              <dt className="font-semibold text-foreground">Descrição</dt>
              <dd className="[overflow-wrap:anywhere] text-foreground">{item.description}</dd>
            </div>
          )}
          <div>
            <dt className="font-semibold text-foreground">Link</dt>
            <dd>
              <a href={item.url} target="_blank" rel="noopener noreferrer" className="inline-flex items-center gap-2 font-semibold break-all text-action hover:underline">
                {hostOf(item.url)}
                <ExternalLink className="size-4 shrink-0" aria-hidden />
                <span className="sr-only">(abre em nova guia, {hostOf(item.url)})</span>
              </a>
            </dd>
          </div>
          {item.anonymous && (
            <div>
              <dt className="font-semibold text-foreground">Autoria</dt>
              <dd className="text-foreground">Compartilhado anonimamente</dd>
            </div>
          )}
        </dl>
      )}

      <div className="mt-4 flex flex-wrap gap-2">
        <Button type="button" variant="outline" aria-expanded={expanded} aria-controls={expanded ? panelId : undefined} onClick={onToggle} className={ACTION}>
          Ver dados enviados
          <ChevronDown className={cn('size-4 transition-transform', expanded && 'rotate-180')} aria-hidden />
        </Button>
        {item.status === 'PENDENTE' && (
          <Button type="button" variant="outline" onClick={onCancel} className={cn(ACTION, 'border-danger text-danger')}>
            Cancelar envio
          </Button>
        )}
        {item.status === 'APROVADO' && (
          <>
            <Button asChild variant="outline" className={ACTION}>
              <Link to={`/estudos/${item.discipline.slug}`}>Ver conteúdo</Link>
            </Button>
            {/* com alteração em análise, uma nova edição seria recusada; o item já mostra a alteração pendente */}
            {onEdit && !item.pending_revision && (
              <Button type="button" variant="outline" onClick={onEdit} className={ACTION}>
                Editar
              </Button>
            )}
            <Button type="button" variant="outline" onClick={onDelete} className={cn(ACTION, 'border-danger text-danger')}>
              Excluir
            </Button>
          </>
        )}
      </div>
    </li>
  );
}
