import { Camera, ClipboardList, ExternalLink, Landmark, Monitor, Phone, School, type LucideIcon } from 'lucide-react';
import { cn } from '@/lib/utils';

type Access = { href: string; title: string; description: string; icon: LucideIcon; tone: string; external: boolean };

// links confirmados pelo time em 08/11/2026 (decisoes.md §71); ícones genéricos, sem logos de terceiros
const ACCESSES: Access[] = [
  {
    href: 'https://www.ifsul.edu.br/',
    title: 'IFSul',
    description: 'Site institucional do Instituto Federal Sul-rio-grandense.',
    icon: Landmark,
    tone: 'bg-success-bg text-success',
    external: true,
  },
  {
    href: 'https://inf.passofundo.ifsul.edu.br/src/home/index.html',
    title: 'Informática / Ciência da Computação',
    description: 'Site da área de Informática do IFSul – Câmpus Passo Fundo.',
    icon: Monitor,
    tone: 'bg-[#e3eefb] text-action',
    external: true,
  },
  {
    href: 'https://suap.ifsul.edu.br/',
    title: 'SUAP',
    description: 'Sistema acadêmico institucional.',
    icon: ClipboardList,
    tone: 'bg-slate-100 text-slate-700',
    external: true,
  },
  {
    href: 'https://moodle.passofundo.ifsul.edu.br/',
    title: 'Moodle',
    description: 'Ambiente Virtual de Aprendizagem.',
    icon: School,
    tone: 'bg-warning-bg text-warning',
    external: true,
  },
  {
    href: 'https://www.instagram.com/ifsulpassofundo/',
    title: 'Instagram do IFSul Passo Fundo',
    description: 'Acompanhe as novidades do câmpus.',
    icon: Camera,
    tone: 'bg-[#fce7f3] text-[#9d174d]',
    external: true,
  },
  {
    href: 'tel:+555433112916',
    title: 'Fale com a CORAC',
    description: 'Tire dúvidas e receba orientações: (54) 3311-2916.',
    icon: Phone,
    tone: 'bg-success-bg text-success',
    external: false,
  },
];

export function InstitutionalLinks() {
  return (
    <section aria-labelledby="acessos-institucionais" className="mt-12">
      <h2 id="acessos-institucionais" className="font-display text-2xl font-bold text-foreground">
        Acessos institucionais
      </h2>
      <p className="mt-1 text-[15px] text-muted-foreground">Conecte-se aos principais serviços do IFSul e do curso.</p>
      <ul className="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        {ACCESSES.map(({ href, title, description, icon: Icon, tone, external }) => (
          <li key={href}>
            <a
              href={href}
              {...(external ? { target: '_blank', rel: 'noopener noreferrer' } : {})}
              className="flex h-full items-center gap-4 rounded-2xl border border-blue-200/70 bg-white p-5 transition-shadow hover:shadow-md"
            >
              <span aria-hidden className={cn('grid size-12 shrink-0 place-items-center rounded-xl', tone)}>
                <Icon className="size-6" />
              </span>
              <span className="min-w-0 flex-1">
                <span className="block font-semibold text-foreground">{title}</span>
                <span className="mt-1 block text-sm text-muted-foreground">{description}</span>
              </span>
              {external ? (
                <>
                  <ExternalLink className="size-5 shrink-0 text-action" aria-hidden />
                  <span className="sr-only">(abre em nova aba)</span>
                </>
              ) : (
                <Phone className="size-5 shrink-0 text-action" aria-hidden />
              )}
            </a>
          </li>
        ))}
      </ul>
    </section>
  );
}
