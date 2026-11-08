import { cn } from '@/lib/utils';

// semântica única em todas as telas (decisoes.md 58); "Alteração" é indicação adicional, nunca substitui o status
const PENDING = { label: 'Em análise', className: 'bg-warning-bg text-warning' };
const TAGS: Record<string, { label: string; className: string }> = {
  PENDENTE: PENDING,
  APROVADO: { label: 'Aprovado', className: 'bg-success-bg text-success' },
  REJEITADO: { label: 'Rejeitado', className: 'bg-danger-bg text-danger' },
  EXCLUIDO: { label: 'Excluído', className: 'bg-danger-bg text-danger' },
  CANCELADO: { label: 'Cancelado', className: 'bg-slate-100 text-muted-foreground' },
  ENCERRADO: { label: 'Encerrado', className: 'bg-slate-100 text-muted-foreground' },
  ALTERACAO: { label: 'Alteração', className: 'bg-change-bg text-change' },
};

export function StatusTag({ status, label }: { status: string; label?: string }) {
  const tag = TAGS[status] ?? PENDING;
  return <span className={cn('shrink-0 rounded-full px-3 py-1 text-sm font-semibold', tag.className)}>{label ?? tag.label}</span>;
}
