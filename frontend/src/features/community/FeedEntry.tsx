import { BookOpen, Briefcase, CalendarDays, FileText, Users } from 'lucide-react';
import { Link } from 'react-router';
import { UserAvatar } from '@/components/layout/UserAvatar';
import { TYPE_LABELS } from '@/features/opportunities/labels';
import { timeAgo } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { FeedItem } from './hooks';

// mesma regra do backend (User::initials): primeiro e último nome
function initialsOf(name: string): string {
  const parts = name.trim().split(/\s+/);
  return ((parts[0]?.[0] ?? '') + (parts.length > 1 ? (parts.at(-1)?.[0] ?? '') : '')).toUpperCase();
}

const linkClass = 'font-semibold text-navy-900 underline-offset-2 hover:underline';

function CommunityAvatar({ icon: Icon }: { icon: typeof Users }) {
  return (
    <span aria-hidden className="grid size-11 shrink-0 place-items-center rounded-full bg-ice text-navy-900">
      <Icon className="size-5" />
    </span>
  );
}

export function FeedEntry({ item, grid = false }: { item: FeedItem; grid?: boolean }) {
  const opportunity = item.kind === 'opportunity';
  const author = item.author_name;
  const Icon = opportunity ? Briefcase : FileText;

  return (
    <article className={cn('flex h-full gap-4 rounded-2xl border border-blue-200/70 bg-white p-5', !grid && 'sm:p-6')}>
      {opportunity ? <CommunityAvatar icon={Briefcase} /> : author ? <UserAvatar initials={initialsOf(author)} /> : <CommunityAvatar icon={Users} />}
      <div className="min-w-0 flex-1">
        <p className="text-base leading-relaxed [overflow-wrap:anywhere] text-foreground">
          {opportunity ? (
            // oportunidade: nunca mostra quem enviou (decisoes.md §70)
            <>
              <span className="font-semibold">Comunidade</span> adicionou uma nova oportunidade em{' '}
              <span className="font-semibold">{TYPE_LABELS[item.opportunity_type ?? ''] ?? item.opportunity_type}</span>:{' '}
              <Link to={`/oportunidade#oportunidade-${item.id}`} className={linkClass}>
                “{item.title}”
              </Link>
            </>
          ) : (
            <>
              {author ? (
                <Link to={item.is_mine ? '/perfil' : `/estudantes/${item.author_code}`} className={linkClass}>
                  {author}
                </Link>
              ) : (
                // anônimo: a API não envia quem enviou, e não há perfil para abrir (decisoes.md §55.2)
                <span className="font-semibold">Comunidade</span>
              )}{' '}
              compartilhou{' '}
              <Link to={`/estudos/${item.discipline?.slug}#material-${item.id}`} className={linkClass}>
                “{item.title}”
              </Link>
            </>
          )}
        </p>
        <p className="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-[15px] text-muted-foreground">
          {item.discipline && (
            <>
              <span className="inline-flex min-w-0 items-center gap-1.5">
                <BookOpen className="size-4 shrink-0" aria-hidden />
                <Link to={`/estudos/${item.discipline.slug}`} className="underline-offset-2 [overflow-wrap:anywhere] hover:text-navy-900 hover:underline">
                  {item.discipline.name}
                </Link>
              </span>
              {item.published_at && <span aria-hidden>•</span>}
            </>
          )}
          {item.published_at && (
            <span className="inline-flex items-center gap-1.5">
              <CalendarDays className="size-4 shrink-0" aria-hidden />
              {timeAgo(item.published_at)}
            </span>
          )}
        </p>
        <span className="mt-2.5 inline-block rounded-full bg-[#dbe7f8] px-2.5 py-0.5 text-sm font-semibold text-navy-900">
          {opportunity ? 'Oportunidade' : 'Material'}
        </span>
      </div>
      {!grid && (
        // por enquanto cada tipo tem um ícone só: o sistema não sabe o tipo do conteúdo do link
        <span aria-hidden className="hidden size-16 shrink-0 place-items-center self-center rounded-2xl bg-[#e3eefb] text-action sm:grid">
          <Icon className="size-7" />
        </span>
      )}
    </article>
  );
}
