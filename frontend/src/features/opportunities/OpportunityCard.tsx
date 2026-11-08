import { CalendarDays, ExternalLink, MapPin } from 'lucide-react';
import { hostOf, timeAgo } from '@/lib/format';
import type { Opportunity } from './hooks';
import { formatDay, MODALITY_LABELS, TYPE_LABELS } from './labels';

// sem autoria: a área pública nunca mostra quem enviou a oportunidade (decisoes.md §70)
export function OpportunityCard({ item }: { item: Opportunity }) {
  const host = hostOf(item.url);

  return (
    <article
      id={`oportunidade-${item.id}`}
      className="flex h-full scroll-mt-28 flex-col gap-2 rounded-2xl border border-blue-200/70 bg-white p-5 transition-shadow duration-500"
    >
      <div className="flex flex-wrap items-center gap-2">
        <span className="rounded-full bg-[#dbe7f8] px-2.5 py-0.5 text-sm font-semibold text-navy-900">
          <span className="sr-only">Tipo: </span>
          {TYPE_LABELS[item.type] ?? item.type}
        </span>
        <span className="inline-flex items-center gap-1 text-sm text-muted-foreground">
          <MapPin className="size-4" aria-hidden />
          <span className="sr-only">Modalidade: </span>
          {MODALITY_LABELS[item.modality] ?? item.modality}
        </span>
      </div>
      <h3 className="font-display text-lg leading-snug font-bold [overflow-wrap:anywhere] text-foreground">{item.title}</h3>
      <p className="text-[15px] [overflow-wrap:anywhere] text-muted-foreground">{item.description}</p>
      <p className="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground">
        {item.deadline && (
          <span className="inline-flex items-center gap-1.5 font-medium text-foreground">
            <CalendarDays className="size-4" aria-hidden />
            Inscrições até {formatDay(item.deadline)}
          </span>
        )}
        {item.published_at && <span>Publicada {timeAgo(item.published_at)}</span>}
      </p>
      <p className="mt-auto flex flex-wrap items-center gap-x-3 gap-y-1 pt-2">
        <a
          href={item.url}
          target="_blank"
          rel="noopener noreferrer"
          className="inline-flex items-center gap-1.5 text-[15px] font-semibold text-action underline-offset-2 hover:underline"
        >
          Acessar oportunidade
          <ExternalLink className="size-4" aria-hidden />
          <span className="sr-only">(abre em nova aba, {host})</span>
        </a>
        <span aria-hidden className="text-sm [overflow-wrap:anywhere] text-muted-foreground">
          {host}
        </span>
      </p>
    </article>
  );
}
