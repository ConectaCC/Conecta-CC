import { Users } from 'lucide-react';
import { Link } from 'react-router';
import { UserAvatar } from '@/components/layout/UserAvatar';
import { timeAgo } from '@/lib/format';
import type { FeedItem } from './hooks';

// mesma regra do backend (User::initials): primeiro e último nome
function initialsOf(name: string): string {
  const parts = name.trim().split(/\s+/);
  return ((parts[0]?.[0] ?? '') + (parts.length > 1 ? (parts.at(-1)?.[0] ?? '') : '')).toUpperCase();
}

const linkClass = 'font-semibold text-navy-900 underline-offset-2 hover:underline';

export function FeedEntry({ item }: { item: FeedItem }) {
  const disciplineUrl = `/estudos/${item.discipline.slug}`;
  const author = item.author_name;

  return (
    <article className="flex gap-4 rounded-2xl border border-blue-200/70 bg-white p-5 sm:p-6">
      {author ? (
        <UserAvatar initials={initialsOf(author)} />
      ) : (
        <span aria-hidden className="grid size-11 shrink-0 place-items-center rounded-full bg-ice text-navy-900">
          <Users className="size-5" />
        </span>
      )}
      <div className="min-w-0 flex-1">
        <p className="text-base leading-relaxed [overflow-wrap:anywhere] text-foreground">
          {author ? (
            <Link to={item.is_mine ? '/perfil' : `/estudantes/${item.author_code}`} className={linkClass}>
              {author}
            </Link>
          ) : (
            // anônimo: a API não envia quem enviou, e não há perfil para abrir (decisoes.md §55.2)
            <span className="font-semibold">Comunidade</span>
          )}{' '}
          compartilhou{' '}
          <Link to={`${disciplineUrl}#material-${item.id}`} className={linkClass}>
            “{item.title}”
          </Link>{' '}
          em{' '}
          <Link to={disciplineUrl} className={linkClass}>
            {item.discipline.name}
          </Link>
        </p>
        {item.published_at && <p className="mt-1 text-sm text-muted-foreground">{timeAgo(item.published_at)}</p>}
      </div>
    </article>
  );
}
