import { ExternalLink, Laptop, type LucideIcon } from 'lucide-react';
import ifsulLogo from '@/assets/institutional/ifsul.png';
import instagramLogo from '@/assets/institutional/instagram.webp';
import moodleLogo from '@/assets/institutional/moodle.svg';
import suapLogo from '@/assets/institutional/suap.png';
import whatsappLogo from '@/assets/institutional/whatsapp.webp';
import { cn } from '@/lib/utils';

type Access = { href: string; title: string; description: string; icon: string | LucideIcon; tone: string };

// links confirmados pelo time em 08/11/2026 (decisoes.md §71); ícones tirados dos próprios sites
const ACCESSES: Access[] = [
  {
    href: 'https://www.ifsul.edu.br/',
    title: 'IFSul',
    description: 'Site institucional do Instituto Federal Sul-rio-grandense.',
    icon: ifsulLogo,
    tone: 'bg-slate-50',
  },
  {
    href: 'https://inf.passofundo.ifsul.edu.br/src/home/index.html',
    title: 'Informática / Ciência da Computação',
    description: 'Site da área de Informática do IFSul – Câmpus Passo Fundo.',
    icon: Laptop,
    tone: 'bg-[#e3eefb] text-action',
  },
  {
    href: 'https://suap.ifsul.edu.br/',
    title: 'SUAP',
    description: 'Sistema acadêmico institucional.',
    icon: suapLogo,
    tone: 'bg-slate-50',
  },
  {
    href: 'https://moodle.passofundo.ifsul.edu.br/',
    title: 'Moodle',
    description: 'Ambiente Virtual de Aprendizagem.',
    icon: moodleLogo,
    tone: 'bg-slate-50',
  },
  {
    href: 'https://www.instagram.com/ifsulpassofundo/',
    title: 'Instagram do IFSul Passo Fundo',
    description: 'Acompanhe as novidades do câmpus.',
    icon: instagramLogo,
    tone: 'bg-slate-50',
  },
  {
    // WhatsApp Web, e não wa.me nem whatsapp://, que tentam abrir o aplicativo instalado
    href: 'https://web.whatsapp.com/send?phone=555433112916',
    title: 'Fale com a CORAC',
    description: 'Tire dúvidas pelo WhatsApp: (54) 3311-2916.',
    icon: whatsappLogo,
    tone: 'bg-slate-50',
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
        {ACCESSES.map(({ href, title, description, icon: Icon, tone }) => (
          <li key={href}>
            <a
              href={href}
              target="_blank"
              rel="noopener noreferrer"
              className="flex h-full items-center gap-4 rounded-2xl border border-blue-200/70 bg-white p-5 transition-shadow hover:shadow-md"
            >
              <span aria-hidden className={cn('grid size-12 shrink-0 place-items-center rounded-xl', tone)}>
                {typeof Icon === 'string' ? <img src={Icon} alt="" className="size-8 object-contain" /> : <Icon className="size-6" />}
              </span>
              <span className="min-w-0 flex-1">
                <span className="block font-semibold text-foreground">{title}</span>
                <span className="mt-1 block text-sm text-muted-foreground">{description}</span>
              </span>
              <ExternalLink className="size-5 shrink-0 text-action" aria-hidden />
              <span className="sr-only">(abre em nova aba)</span>
            </a>
          </li>
        ))}
      </ul>
    </section>
  );
}
