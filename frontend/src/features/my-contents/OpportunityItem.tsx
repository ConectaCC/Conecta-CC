import { ExternalLink } from 'lucide-react';
import { Link } from 'react-router';
import { StatusTag } from '@/components/StatusTag';
import { Button } from '@/components/ui/button';
import { formatDay, MODALITY_LABELS, TYPE_LABELS } from '@/features/opportunities/labels';
import { formatDate, hostOf } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { MyOpportunity } from './hooks';

// oportunidade é substantivo feminino: as tags usam a forma certa
const STATUS_LABELS: Record<string, string> = {
  PENDENTE: 'Em análise',
  APROVADO: 'Aprovada',
  REJEITADO: 'Rejeitada',
  CANCELADO: 'Cancelada',
  EXCLUIDO: 'Excluída',
  ENCERRADO: 'Encerrada',
};

const ACTION = 'h-10 border-action px-4 text-[15px] font-semibold text-action';

type Props = { item: MyOpportunity; onCancel: () => void; onDelete: () => void };

export function OpportunityItem({ item, onCancel, onDelete }: Props) {
  const host = hostOf(item.url);

  return (
    <li id={`item-${item.code}`} className="scroll-mt-24 rounded-2xl border border-blue-200/70 bg-white p-6">
      <div className="flex flex-wrap items-start justify-between gap-3">
        <div className="min-w-0">
          <p className="text-xs font-bold tracking-wide text-muted-foreground uppercase [overflow-wrap:anywhere]">
            {item.code} · Oportunidade · {TYPE_LABELS[item.type] ?? item.type}
          </p>
          <h2 className="mt-1 font-display text-lg font-bold [overflow-wrap:anywhere] text-foreground">{item.title}</h2>
          <p className="mt-1 text-[15px] text-muted-foreground">
            Enviada em {formatDate(item.submitted_at)} · {MODALITY_LABELS[item.modality] ?? item.modality}
            {item.deadline ? ` · Inscrições até ${formatDay(item.deadline)}` : ''}
          </p>
        </div>
        <StatusTag status={item.status} label={STATUS_LABELS[item.status]} />
      </div>

      <p className="mt-3 text-[15px] [overflow-wrap:anywhere] text-foreground">{item.description}</p>
      {item.status === 'ENCERRADO' && (
        <p className="mt-2 text-[15px] text-muted-foreground">O prazo terminou, e a oportunidade saiu da página.</p>
      )}
      {item.status === 'REJEITADO' && item.rejection_reason && (
        <p className="mt-3 rounded-lg bg-danger-bg px-4 py-3 text-[15px] [overflow-wrap:anywhere] text-foreground">
          <strong>Motivo da rejeição:</strong> {item.rejection_reason}
        </p>
      )}

      <div className="mt-4 flex flex-wrap items-center gap-2">
        <a
          href={item.url}
          target="_blank"
          rel="noopener noreferrer"
          className="mr-2 inline-flex items-center gap-1.5 text-[15px] font-semibold break-all text-action hover:underline"
        >
          {host}
          <ExternalLink className="size-4 shrink-0" aria-hidden />
          <span className="sr-only">(abre em nova guia, {host})</span>
        </a>
        {item.status === 'PENDENTE' && (
          <Button type="button" variant="outline" onClick={onCancel} className={cn(ACTION, 'border-danger text-danger')}>
            Cancelar envio
          </Button>
        )}
        {(item.status === 'APROVADO' || item.status === 'ENCERRADO') && (
          <>
            {item.status === 'APROVADO' && (
              <Button asChild variant="outline" className={ACTION}>
                <Link to={`/oportunidade#oportunidade-${item.id}`}>Ver na página</Link>
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
