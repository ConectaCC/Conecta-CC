import { ExternalLink } from 'lucide-react';
import { hostOf, timeAgo } from '@/lib/format';
import type { Material } from './hooks';

export function MaterialCard({ material }: { material: Material }) {
  const host = hostOf(material.url);
  const when = material.published_at ? ` · ${timeAgo(material.published_at)}` : '';

  return (
    <article className="rounded-2xl border border-blue-200/70 bg-white p-6">
      <h3 className="font-display text-lg font-bold leading-snug [overflow-wrap:anywhere] text-foreground">{material.title}</h3>
      {material.description && <p className="mt-2 text-base [overflow-wrap:anywhere] text-muted-foreground">{material.description}</p>}
      <p className="mt-2 text-[15px] text-muted-foreground">
        {material.author_name ? (
          <>
            Compartilhado por <span className="font-semibold text-foreground">{material.author_name}</span>
            {when}
          </>
        ) : (
          `Compartilhado pela comunidade${when}`
        )}
      </p>
      <p className="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1">
        <a
          href={material.url}
          target="_blank"
          rel="noopener noreferrer"
          className="inline-flex items-center gap-2 font-semibold text-action hover:underline"
        >
          Acessar material
          <ExternalLink className="size-4" aria-hidden />
          <span className="sr-only">(abre em nova guia, {host})</span>
        </a>
        <span aria-hidden className="text-sm break-all text-muted-foreground">
          {host}
        </span>
      </p>
    </article>
  );
}
