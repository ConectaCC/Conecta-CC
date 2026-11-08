import { BookOpen, CalendarDays, ExternalLink, Layers, type LucideIcon } from 'lucide-react';

type Resource = { href: string; label: string; icon: LucideIcon };

// links confirmados pelo time em 08/11/2026 (decisoes.md §72)
const RESOURCES: Resource[] = [
  { href: 'https://plataforma.bvirtual.com.br/Account/Login?redirectUrl=%2F', label: 'Biblioteca Virtual', icon: BookOpen },
  { href: 'https://inf.passofundo.ifsul.edu.br/src/Calendario/calendario.html', label: 'Calendário Acadêmico', icon: CalendarDays },
];

export function AcademicResources() {
  return (
    <section aria-labelledby="recursos-academicos" className="min-w-0">
      <h2 id="recursos-academicos" className="flex items-center gap-2 font-display text-lg font-bold text-foreground">
        <Layers className="size-5 text-action" aria-hidden />
        Recursos acadêmicos
      </h2>
      <ul className="mt-3 flex flex-wrap gap-3">
        {RESOURCES.map(({ href, label, icon: Icon }) => (
          <li key={href}>
            <a
              href={href}
              target="_blank"
              rel="noopener noreferrer"
              className="flex h-14 items-center gap-3 rounded-2xl bg-white px-4 font-semibold text-foreground shadow-sm transition-shadow hover:shadow-md"
            >
              <Icon className="size-6 text-action" aria-hidden />
              {label}
              <ExternalLink className="size-5 text-action" aria-hidden />
              <span className="sr-only">(abre em nova aba)</span>
            </a>
          </li>
        ))}
      </ul>
    </section>
  );
}
